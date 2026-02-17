import React, {useState} from "react";
import Layout from "../components/Layout";
import vp_safe from '../images/vp_safe.png'
import vp_cc from '../images/vp_kreditkarte.png'
// import {BsInfoCircle} from "react-icons/all";
import {router} from '@inertiajs/react'
import SafePayInfo from "../components/SafePayInfo";

export default (props) => {
    const handleSubmit = (e) => {
        e.preventDefault();
        router.post('/', {pin: document.getElementById('pin').value});
    };

    return (
        <Layout>
            <div className="bg-main-white py-8 px-4 grid grid-cols-3 lg:grid-cols-2">

                <div className="col-span-3 sm:col-span-2 lg:col-span-1">
                    <div className="text-main-black-title font-black">
                        PIN Eingabe
                    </div>
                    <div className="max-w-sm mt-4">
                        <div className="border-0 border-gray-300 border-t py-4">

                            <form className="" onSubmit={handleSubmit}>
                                <div className="flex flex-row gap-12 items-center w-full flex-grow">
                                    <label htmlFor="pin" className="block text-sm text-main-black-title font-medium">
                                        PIN
                                    </label>
                                    <div className="w-full">
                                        <input type="text" name="pin" id="pin"
                                               className="block w-full rounded-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-main-green-bg sm:text-sm sm:leading-6"
                                               placeholder="PIN-Code aus unserer SMS"
                                               required
                                        />
                                    </div>
                                </div>
                                {props.error &&
                                    <div className="text-xs text-red-700 text-end mt-1">
                                        {props.error}
                                    </div>
                                }
                                <div className="flex justify-end mt-4">
                                    <button type="submit"
                                            className="rounded-full bg-main-green-bg py-2.5 px-3.5 text-sm font-semibold text-main-dark-green shadow-sm hover:bg-main-green-bg-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-main-green-bg">
                                        Bestätigen
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
                            Dein PIN-Code
                        </div>
                        <div className="mt-3">
                            Wir haben dir vor Kurzem eine SMS mit einem PIN-Code gesendet. Gib diesen Code hier ein, um
                            zur Sicher-Bezahlen
                            Seite des Produktes zu kommen und unseren Käuferschutz zu verwenden.
                        </div>
                    </div>
                </div>

            </div>

            <SafePayInfo />

        </Layout>
    );
};
