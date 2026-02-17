import React from "react";
// import {BsInfoCircle} from "react-icons/all";

export default (props) => {

    const {data, handleSubmitBack} = props;

    return (
        <div className="bg-main-white py-8 px-4 grid grid-cols-3 lg:grid-cols-2 mt-3">

            <div className="col-span-3 sm:col-span-2 lg:col-span-1">
                <div className="text-main-black-title font-black">
                    Sicher Versenden
                </div>
                <div className="max-w-sm mt-4">
                    <div className="border-0 border-gray-300 border-t py-4">

                        <form className="" onSubmit={handleSubmitBack}>
                            <div className="flex flex-col w-full gap-4">
                                <div className="w-32 block text-main-black-title font-medium">
                                    Versandadresse
                                </div>
                                <div>
                                    <div className="w-full text-sm">
                                        {data.firstname} {data.lastname}
                                    </div>
                                    <div className="w-full text-sm">
                                        {data.address}
                                    </div>
                                    <div className="w-full text-sm">
                                        {data.zip} {data.city}
                                    </div>
                                    <div className="w-full text-sm">
                                        {data.country}
                                    </div>
                                </div>
                            </div>
                            <div className="flex flex-col w-full gap-4 mt-8">
                                <div className="w-32 block text-main-black-title font-medium">
                                    E-Mail Adresse
                                </div>
                                <div>
                                    <div className="w-full text-sm">
                                        {data.email}
                                    </div>
                                </div>
                            </div>
                            <div className="flex justify-end mt-4">
                                <button type="submit"
                                        className="rounded-full bg-main-green-bg py-2.5 px-3.5 text-sm font-semibold text-main-dark-green shadow-sm hover:bg-main-green-bg-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-main-green-bg">
                                    Ändern
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
                        Deine Adresse
                    </div>
                    <div className="mt-3">
                        Hierhin wird der Verkäufer das Produkt versenden.
                    </div>
                    <div className="mt-2">
                        Falls die Adresse nicht stimmt, kannst du sie jetzt noch ändern.
                    </div>
                </div>
            </div>

        </div>
    );
};
