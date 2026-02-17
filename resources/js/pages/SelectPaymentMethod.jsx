import React, {useState} from "react";
import Layout from "../components/Layout";
import sepa from '../images/sepa.png'
import cc from '../images/cc.png'
// import {AiOutlineInfoCircle, BsInfoCircle} from "react-icons/all";
import {router} from "@inertiajs/react";
import ProductCard from "../components/ProductCard";
import SafePayInfo from "../components/SafePayInfo";

export default function(props) {
    const pin = props.pin;
    const productPage = props.productPage;
    const imageLinks = props.imageLinks;
    const data = props.data;

    const [method, setMethod] = useState('sepa');

    const handleSubmit = (e) => {
        e.preventDefault();
        router.post('/' + pin + '/bezahlen', {
            method: method,
        });
    };

    const handleSubmitBack = (e) => {
        e.preventDefault();
        router.post('/', {pin: pin});
    };

    return (
        <Layout>
            <div className="bg-main-white py-8 px-4 grid grid-cols-3 lg:grid-cols-2">

                <div className="col-span-3 sm:col-span-2 lg:col-span-1">
                    <div className="text-main-black-title font-black">
                        Wie möchtest du zahlen?
                    </div>
                    <div className="max-w-sm mt-4">
                        <div className="border-0 border-gray-300 border-t py-4">

                            <form className="" onSubmit={handleSubmit}>

                                <div className="flex items-center justify-between gap-x-4">
                                    <div className="flex gap-x-6">
                                        <div className={"h-20 w-auto rounded-md cursor-pointer border-4 hover:border-main-green-bg-hover " + (method === 'sepa' ? 'border-main-green-bg' : 'border-transparent')}
                                             onClick={() => setMethod('sepa')}
                                        >
                                            <img src={sepa} className="h-full w-auto" />
                                        </div>
                                    </div>

                                    <div className="flex gap-x-6">
                                        <div className={"h-20 w-auto rounded-md cursor-pointer border-4 hover:border-main-green-bg-hover " + (method === 'cc' ? ' border-main-green-bg' : 'border-transparent')}
                                            onClick={() => setMethod('cc')}
                                        >
                                            <img src={cc} className="h-full w-auto" />
                                        </div>
                                    </div>
                                </div>

                                <div className="flex justify-end mt-4">
                                    <button type="submit"
                                            className="rounded-full bg-main-green-bg py-2.5 px-3.5 text-sm font-semibold text-main-dark-green shadow-sm hover:bg-main-green-bg-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-main-green-bg">
                                        Weiter
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
                            Bezahlmethode
                        </div>
                        <div className="mt-3">
                            Wir bieten SEPA Überweisung, SEPA Echtzeit Überweisung und Zahlung per Kreditkarte als Zahlungsmethode an.
                            Bitte wähle deine bevorzugte Art zu zahlen und folge unseren Anweisungen, um den Kauf sicher abzuschließen.
                        </div>
                    </div>
                </div>

            </div>

            <ProductCard productPage={productPage} imageLinks={imageLinks} />

            <SafePayInfo />

        </Layout>
    );
};
