import React from "react";
import Layout from "../components/Layout";
// import {BsInfoCircle} from "react-icons/all";
import {router} from "@inertiajs/react";
import ProductCard from "../components/ProductCard";
import SafePayInfo from "../components/SafePayInfo";

export default (props) => {
    const pin = props.pin;
    const productPage = props.productPage;
    const imageLinks = props.imageLinks;
    const formErrors = props.errors ?? {};

    const handleSubmit = (e) => {
        e.preventDefault();
        router.post('/' + pin + '/methode', {
            firstname: document.getElementById('firstname').value,
            lastname: document.getElementById('lastname').value,
            address: document.getElementById('address').value,
            zip: document.getElementById('zip').value,
            city: document.getElementById('city').value,
            country: document.getElementById('country').value,
            email: document.getElementById('email').value,
        });
    };

    return (
        <Layout>
            <div className="bg-main-white py-8 px-4 grid grid-cols-3 lg:grid-cols-2">

                <div className="col-span-3 sm:col-span-2 lg:col-span-1">
                    <div className="text-main-black-title font-black">
                        Sicher Versenden
                    </div>
                    <div className="max-w-sm mt-4">
                        <div className="border-0 border-gray-300 border-t py-4">

                            <form className="" onSubmit={handleSubmit}>
                                <div className="flex flex-row gap-12 items-center w-full flex-grow">
                                    <label htmlFor="firstname"
                                           className="w-32 block text-sm text-main-black-title font-medium">
                                        Vorname
                                    </label>
                                    <div className="w-full">
                                        <input type="text" name="firstname" id="firstname"
                                               className={`block w-full rounded-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ${formErrors.firstname ? 'ring-red-300 focus:ring-red-300' : 'ring-gray-300 focus:ring-main-green-bg'} placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6`}
                                               placeholder="Dein Vorname"
                                               required
                                        />
                                        {formErrors.firstname &&
                                            <div className="text-xs text-red-700">
                                                Der Vorname ist ungültig!
                                            </div>
                                        }
                                    </div>
                                </div>
                                <div className="flex flex-row gap-12 items-center w-full flex-grow mt-2">
                                    <label htmlFor="lastname"
                                           className="w-32 block text-sm text-main-black-title font-medium">
                                        Nachname
                                    </label>
                                    <div className="w-full">
                                        <input type="text" name="lastname" id="lastname"
                                               className={`block w-full rounded-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ${formErrors.lastname ? 'ring-red-300 focus:ring-red-300' : 'ring-gray-300 focus:ring-main-green-bg'} placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6`}
                                               placeholder="Dein Nachname"
                                               required
                                        />
                                        {formErrors.lastname &&
                                            <div className="text-xs text-red-700">
                                                Der Nachname ist ungültig!
                                            </div>
                                        }
                                    </div>
                                </div>
                                <div className="flex flex-row gap-12 items-center w-full flex-grow mt-2">
                                    <label htmlFor="address"
                                           className="w-32 block text-sm text-main-black-title font-medium">
                                        Adresse
                                    </label>
                                    <div className="w-full">
                                        <input type="text" name="address" id="address"
                                               className={`block w-full rounded-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ${formErrors.address ? 'ring-red-300 focus:ring-red-300' : 'ring-gray-300 focus:ring-main-green-bg'} placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6`}
                                               placeholder="Straße und Hausnummer"
                                               required
                                        />
                                        {formErrors.address &&
                                            <div className="text-xs text-red-700">
                                                Die Adresse ist ungültig!
                                            </div>
                                        }
                                    </div>
                                </div>
                                <div className="flex flex-row gap-12 items-center w-full flex-grow mt-2">
                                    <label htmlFor="zip"
                                           className="w-32 block text-sm text-main-black-title font-medium">
                                        Postleitzahl
                                    </label>
                                    <div className="w-full">
                                        <input type="text" name="zip" id="zip"
                                               className={`block w-full rounded-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ${formErrors.zip ? 'ring-red-300 focus:ring-red-300' : 'ring-gray-300 focus:ring-main-green-bg'} placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6`}
                                               placeholder="Deine Postleitzahl"
                                               required
                                        />
                                        {formErrors.zip &&
                                            <div className="text-xs text-red-700">
                                                Die Postleitzahl ist ungültig!
                                            </div>
                                        }
                                    </div>
                                </div>
                                <div className="flex flex-row gap-12 items-center w-full flex-grow mt-2">
                                    <label htmlFor="city"
                                           className="w-32 block text-sm text-main-black-title font-medium">
                                        Stadt
                                    </label>
                                    <div className="w-full">
                                        <input type="text" name="city" id="city"
                                               className={`block w-full rounded-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ${formErrors.city ? 'ring-red-300 focus:ring-red-300' : 'ring-gray-300 focus:ring-main-green-bg'} placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6`}
                                               placeholder="Deine Stadt"
                                               required
                                        />
                                        {formErrors.city &&
                                            <div className="text-xs text-red-700">
                                                Die Stadt ist ungültig!
                                            </div>
                                        }
                                    </div>
                                </div>
                                <div className="flex flex-row gap-12 items-center w-full flex-grow mt-2">
                                    <label htmlFor="country"
                                           className="w-32 block text-sm text-main-black-title font-medium">
                                        Land
                                    </label>
                                    <div className="w-full">
                                        <input type="text" name="country" id="country"
                                               className={`block w-full rounded-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ${formErrors.country ? 'ring-red-300 focus:ring-red-300' : 'ring-gray-300 focus:ring-main-green-bg'} placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6`}
                                               placeholder="Dein Land"
                                               required
                                        />
                                        {formErrors.country &&
                                            <div className="text-xs text-red-700">
                                                Das Land ist ungültig!
                                            </div>
                                        }
                                    </div>
                                </div>
                                <div className="flex flex-row gap-12 items-center w-full flex-grow mt-6">
                                    <label htmlFor="email"
                                           className="w-32 block text-sm text-main-black-title font-medium">
                                        E-Mail
                                    </label>
                                    <div className="w-full">
                                        <input type="email" name="email" id="email"
                                               className={`block w-full rounded-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ${formErrors.email ? 'ring-red-300 focus:ring-red-300' : 'ring-gray-300 focus:ring-main-green-bg'} placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6`}
                                               placeholder="Deine E-Mail Adresse"
                                               required
                                        />
                                        {formErrors.email &&
                                            <div className="text-xs text-red-700">
                                                Die E-Mail ist ungültig!
                                            </div>
                                        }
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
                            Deine Versand-Adresse
                        </div>
                        <div className="mt-3">
                            Bitte gib deine Versand-Adresse an. Diese wird dem Verkäufer mitgeteilt,
                            damit er dir das Produkt dort hinschicken kann, sobald wir deine Überweisung erhalten haben.
                        </div>
                        <div className="mt-2">
                            Sobald das Produkt bei dir ist, kannst du den überwiesenen Betrag freigeben, damit
                            wir dein Geld dem Verkäufer weiterleiten.
                        </div>
                        <div className="mt-2">
                            Falls mit der Sendung etwas nicht stimmt, ist dein Geld sicher bei uns aufbewahrt und du
                            bekommst den vollen Betrag von uns erstattet.
                        </div>
                    </div>
                </div>

            </div>

            <ProductCard productPage={productPage} imageLinks={imageLinks} />

            <SafePayInfo />

        </Layout>
    );
};
