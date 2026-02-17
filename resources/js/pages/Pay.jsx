import React from "react";
import Layout from "../components/Layout";
import vp_safe from '../images/vp_safe.png'
import vp_cc from '../images/vp_kreditkarte.png'
// import {AiOutlineInfoCircle, BsInfoCircle} from "react-icons/all";
import {router} from "@inertiajs/react";
import ProductCard from "../components/ProductCard";
import SafePayInfo from "../components/SafePayInfo";
import PriceOverview from "../components/PriceOverview";
import PayDeliveryInfo from "../components/PayDeliveryInfo";

export default (props) => {
    const pin = props.pin;
    const productPage = props.productPage;
    const imageLinks = props.imageLinks;
    const data = props.data;

    const handleSubmit = (e) => {
        e.preventDefault();
        router.post('/' + pin + '/bezahlt', {});
    };

    const handleSubmitBack = (e) => {
        e.preventDefault();
        router.get('/' + pin + '/methode', {});
    };

    const formatter = new Intl.NumberFormat('de-DE', {style: 'currency', currency: 'EUR'});
    const formattedPrice = formatter.format(productPage.price);
    const formattedFees = formatter.format(productPage.fees * productPage.price);
    const formattedShipping = formatter.format(productPage.shipping_price ?? 0);

    const formattedAmount = formatter.format(productPage.price + (productPage.fees * productPage.price) + (productPage.shipping_price ?? 0));

    return (
        <Layout>
            <div className="bg-main-white py-8 px-4 grid grid-cols-3 lg:grid-cols-2">

                <div className="col-span-3 sm:col-span-2 lg:col-span-1">
                    <div className="text-main-black-title font-black">
                        Sicher Bezahlen
                    </div>
                    <div className="max-w-sm mt-4">
                        <div className="border-0 border-gray-300 border-t py-4">

                            <PriceOverview
                                formattedPrice={formattedPrice}
                                formattedFees={formattedFees}
                                formattedShipping={formattedShipping}
                                formattedAmount={formattedAmount}
                            />

                            <form className="mt-12" onSubmit={handleSubmit}>
                                <div className="flex flex-row gap-12 items-center w-full flex-grow">
                                    <div className="w-32 block text-sm text-main-black-title font-medium">
                                        Empfänger
                                    </div>
                                    <div className="w-full text-sm">
                                        {productPage.iban_name}
                                    </div>
                                </div>
                                <div className="flex flex-row gap-12 items-center w-full flex-grow mt-2">
                                    <div className="w-32 block text-sm text-main-black-title font-medium">
                                        IBAN
                                    </div>
                                    <div className="w-full text-sm">
                                        {productPage.iban}
                                    </div>
                                </div>
                                {productPage.bic &&
                                    <div className="flex flex-row gap-12 items-center w-full flex-grow mt-2">
                                        <div className="w-32 block text-sm text-main-black-title font-medium">
                                            BIC
                                        </div>
                                        <div className="w-full text-sm">
                                            {productPage.bic}
                                        </div>
                                    </div>
                                }
                                <div className="flex flex-row gap-12 items-center w-full flex-grow mt-2">
                                    <div className="w-32 block text-sm text-main-black-title font-medium">
                                        Betrag
                                    </div>
                                    <div className="w-full text-sm">
                                        {formattedAmount}
                                    </div>
                                </div>
                                <div className="flex flex-col w-full mt-6 gap-2">
                                    <div className="w-32 block text-sm text-main-black-title font-medium">
                                        Verwendungszweck
                                    </div>
                                    <div className="w-full text-sm">
                                        {pin}, {productPage.seller_klaz_user_id}
                                    </div>
                                </div>
                                <div className="flex justify-between mt-8">
                                    <button type="button"
                                            className="rounded-full bg-gray-300 py-2.5 px-3.5 text-sm font-semibold text-gray-600 shadow-sm hover:bg-gray-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-main-green-bg"
                                            onClick={handleSubmitBack}
                                    >
                                        Zurück
                                    </button>
                                    <button type="submit"
                                            className="rounded-full bg-main-green-bg py-2.5 px-3.5 text-sm font-semibold text-main-dark-green shadow-sm hover:bg-main-green-bg-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-main-green-bg">
                                        Ich habe bezahlt
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                <div className="bg-main-gray rounded-lg p-4 max-w-sm col-span-3 sm:col-span-1 lg:min-w-[20rem] ml-auto">
                    <div className="text-main-black-text">
                        <div className="flex flex-row items-center gap-3 font-semibold">
                            {/* <BsInfoCircle className="w-5 h-5"/> */}
                            Bezahlung des Produktes
                        </div>
                        <div className="mt-3">
                            Bitte überweise den Betrag von <span className="font-semibold">{formattedAmount}</span> auf
                            unser Bankkonto.
                        </div>
                        <div className="mt-2">
                            Die Käuferschutzgebühr setzt sich aus einer geringen Pauschale von 0,35 €
                            sowie 4,5 % des Preises, auf den du dich mit dem Verkäufer geeinigt hast, zusammen.
                        </div>
                        <div className="mt-2">
                            Nach dem Geldeingang erhält der Verkäufer die Aufforderung, das gekaufte Produkt an deine
                            Adresse zu verschicken.
                            Du wirst dazu per E-Mail benachrichtigt und erhältst eine Zahlungsbestätigung.
                        </div>
                        <div className="mt-2">
                            Sobald das Produkt bei dir angekommen ist, kannst du das Geld, das du an uns überwiesen
                            hast, für den Verkäufer freigeben.
                            Wie das funktioniert, ist in der E-Mail beschrieben, die wir dir schicken.
                        </div>
                    </div>
                </div>

            </div>

            <PayDeliveryInfo
                data={data}
                handleSubmitBack={handleSubmitBack}
            />

            <ProductCard productPage={productPage} imageLinks={imageLinks}/>

            <SafePayInfo/>

        </Layout>
    );
};
