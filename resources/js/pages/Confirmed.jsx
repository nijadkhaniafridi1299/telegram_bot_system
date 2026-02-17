import React from "react";
import Layout from "../components/Layout";
import vp_safe from '../images/vp_safe.png'
import vp_cc from '../images/vp_kreditkarte.png'
// import {AiOutlineCheckCircle, AiOutlineInfoCircle, BsInfoCircle} from "react-icons/all";
import {router} from "@inertiajs/react";
import ProductCard from "../components/ProductCard";
import SafePayInfo from "../components/SafePayInfo";

export default (props) => {
    const pin = props.pin;
    const productPage = props.productPage;
    const imageLinks = props.imageLinks;

    return (
        <Layout>
            <div className="bg-main-white py-8 px-4 grid grid-cols-3 lg:grid-cols-2">

                <div className="col-span-3 sm:col-span-2 lg:col-span-1">
                    <div className="text-main-black-title font-black flex flex-row gap-3 items-center">
                        {/* <AiOutlineCheckCircle className="w-8 h-8 text-main-price-color"/> Kauf abgeschlossen */}
                    </div>
                    <div className="max-w-sm mt-4">
                        <div className="border-0 border-gray-300 border-t py-4">

                            <div className="flex flex-col gap-3">
                                <div className="text-sm">
                                    Wir warten nun auf den Eingang deiner Überweisung. Dies kann 1-3 Werktage dauern.
                                </div>
                                <div className="text-sm">
                                    Sobald das Geld eingegangen ist,
                                    bekommst du und der Verkäufer eine Benachrichtigung. Der Verkäufer wird zudem aufgefordert,
                                    dein Produkt zu verschicken.
                                </div>
                                <div className="text-sm">
                                    Das Geld wird von uns solange einbehalten, bis du es freigibst. Erst dann erhält der Verkäufer
                                    Zugriff auf den Kaufbetrag.
                                </div>
                            </div>

                        </div>
                    </div>

                    <div className="mt-6">
                        <a href={productPage.klaz_url}
                           className="rounded-full bg-main-green-bg py-2.5 px-3.5 text-sm font-semibold text-main-dark-green shadow-sm hover:bg-main-green-bg-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-main-green-bg">
                            Zurück zu Kleinanzeigen
                        </a>
                    </div>

                </div>

            </div>

            <ProductCard productPage={productPage} imageLinks={imageLinks} />

            <SafePayInfo />

        </Layout>
    );
};
