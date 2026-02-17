<?php

namespace App\Actions;

use App\Exceptions\ScrapeException;
use App\Models\Proxy;
use App\Utils\KlazAd;
use Carbon\Carbon;
use Faker\Provider\UserAgent;
use Http;
use PHPHtmlParser\Dom;
use Str;
use Throwable;
use function collect;
use function explode;
use function floatval;
use function intval;
use function json_decode;
use function mb_strpos;
use function preg_split;
use function str_split;
use function trim;

class GrabKlazAd
{

    public function grab(string $url): KlazAd
    {
        $rawHtml = $this->scrapeRawUrl($url);
        $dom = new Dom();
        $dom->loadStr($rawHtml);

        $lines = collect(preg_split("/\r\n|\n|\r/", $rawHtml));

        $identifier = 'Belen.LibertyWrapper.init(';
        $identifierViewUrl = '"viewAdVisitCounterUrl": "';
        $identifierSellerId = '"sellerId": ';
        $data = null;
        $viewAdVisitCounterUrl = null;
        $sellerId = null;

        foreach ($lines as $line) {
            if ($data == null && Str::contains($line, $identifier)) {
                $startIndex = mb_strpos($line, $identifier);
                $jsonStrings = trim(Str::substr($line, $startIndex + Str::length($identifier)));
                $jsonStrings = Str::substr($jsonStrings, 0, -2);

                $jsons = collect();

                $chars = str_split($jsonStrings);
                $openings = 0;
                $closings = 0;
                $buffer = '';
                foreach ($chars as $char) {
                    if ($openings == 0 && $char != '{') continue;

                    if ($char == '{') {
                        $openings++;
                    } elseif ($char == '}') {
                        $closings++;
                    }

                    $buffer .= $char;

                    if ($openings == $closings && $openings > 0) {
                        $jsons->add($buffer);
                        $buffer = '';
                        $openings = 0;
                        $closings = 0;
                    }
                }

                $dataCol = $jsons->map(fn($json) => json_decode($json, true));

                $data = $dataCol[1];
            }

            if ($viewAdVisitCounterUrl == null && Str::contains($line, $identifierViewUrl)) {
                $startIndex = mb_strpos($line, $identifierViewUrl);
                $viewAdVisitCounterUrl = trim(Str::substr($line, $startIndex + Str::length($identifierViewUrl)));
                $viewAdVisitCounterUrl = Str::substr($viewAdVisitCounterUrl, 0, -2);
            }

            if ($sellerId == null && Str::contains($line, $identifierSellerId)) {
                $startIndex = mb_strpos($line, $identifierSellerId);
                $sellerId = trim(Str::substr($line, $startIndex + Str::length($identifierSellerId)));
                $sellerId = Str::substr($sellerId, 0, -1);
            }

            if ($viewAdVisitCounterUrl != null && $data != null && $sellerId == null) {
                break;
            }
        }

        $dataDetails = $data["%ENCODED_BIDDER_CUSTOM_PARAMS%"] ?? $data["%DFP_TARGETS%"] ?? $data["%BIDDER_CUSTOM_PARAMS%"];

//        $city = Str::replace('_', ' ', $dataDetails['city']);
//        if (!empty($dataDetails['region'])) {
//            $city = trim(Str::replace($dataDetails['region'], '', $city));
//        }

        $element = $dom->find('#vip-ad-creationdate')[0];
        $dateParts = explode('.', trim($element->text));
        $createdAt = Carbon::createFromDate(intval($dateParts[2]), intval($dateParts[1]), intval($dateParts[0]));

        $element = $dom->find('#viewad-description-text')[0];
        $description = Str::replace(['<br>', '</br>', '<br/>', '<br />'], "\n", trim($element->innerhtml));

        $element = $dom->find('#vip-ad-address')[0];
        $address = trim($element->text);
        $addressParts = explode(' ', $address, 2);
        $zip = $addressParts[0];
        $city = $addressParts[1];

        $element = $dom->find('#vip-userprofile div.userprofile-teaser--info > h2.userprofile-teaser--title')[0];
        $sellerName = trim($element->text);

        $element = $dom->find('#vip-ad-header > div.ad-keydetails--price-and-shipping > div.ad-keydetails--shipping-header')[0];
        $shippingPrice = Str::replace(['+ Versand ab ', ' €'], '', trim($element->text));
        $shippingEnabled = !empty($shippingPrice);
        $shippingPrice = floatval(Str::replace(',', '.', $shippingPrice));

//        $viewAdVisitCounterUrlParts = parse_url($viewAdVisitCounterUrl);
//        parse_str($viewAdVisitCounterUrlParts['query'], $viewAdVisitCounterUrlQuery);
//        $sellerId = $viewAdVisitCounterUrlQuery['userId'];

        /** @var Dom\Collection $elements */
        $elements = $dom->find('#vip-ad-picture-list > li.imagegallery--item > img');

        $imageLinks = collect([]);
        foreach ($elements as $element) {
            $src = Str::replace('&#61;', '=', $element->getAttribute('src'));
            $imageLinks->add($src);
        }

        return new KlazAd(
            intval($dataDetails['g_adid']),
            $data['%QUERY%'],
            intval($dataDetails['ExactPreis']),
            $shippingEnabled,
            $shippingPrice,
            $zip ?? $dataDetails['plz'],
            $city,
            $description,
            $sellerName,
            $sellerId,
            $imageLinks->all(),
            $createdAt,
        );
    }

    private function scrapeRawUrl(string $url, int $attempts = 5): string
    {
        $latestException = null;

        for ($attempt = 0; $attempt < $attempts; $attempt++) {
            try {
                $proxy = Proxy::inRandomOrder()->where('enabled', '=', true)->first();

                $options = [];
                if ($proxy != null) {
                    $options['proxy'] = "http://{$proxy->user}:{$proxy->password}@{$proxy->host}:{$proxy->port}";
                }

                $response = Http::withUserAgent(UserAgent::chrome())
                    ->timeout(25)
                    ->retry(2)
                    ->withOptions($options)
                    ->get($url);

                if (!$response->successful()) {
                    throw new ScrapeException('Unable to http get ' . $url);
                }

                return $response->body();
            } catch (Throwable $exception) {
                $latestException = $exception;
            }
        }

        throw $latestException;
    }

}
