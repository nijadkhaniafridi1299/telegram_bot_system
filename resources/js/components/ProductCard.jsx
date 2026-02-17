import React from "react";
// import {BiUserCircle, GoLocation} from "react-icons/all";
import SimpleImageSlider from "react-simple-image-slider";

export default (props) => {
    const productPage = props.productPage;
    const imageLinks = props.imageLinks.map(link => ({
        url: link
    }));

    return (
        <div>
            <div className="mt-12 bg-white flex items-center justify-center mx-auto relative">
                <SimpleImageSlider
                    width="100%"
                    height="30rem"
                    images={imageLinks}
                    showBullets={true}
                    showNavs={true}
                />
            </div>

            <div className="mt-3 bg-white py-4 px-4">

                <div className="text-main-black-title text-xl font-black flex flex-row gap-3 items-center">
                    {productPage.title}
                </div>
                <div className="flex flex-row gap-2 items-end mt-2">
                    <div className="text-xl font-black text-main-price-color">
                        {productPage.price} €
                    </div>
                    {productPage.shipping_price != null &&
                        <div className="text-sm text-gray-500">
                            + {(productPage.shipping_price === 0 ? '0,00' : productPage.shipping_price).toString().replace('.', ',')} € Versand
                        </div>
                    }
                </div>

                <a className="mt-6 flex flex-row gap-2 text-gray-500 items-center text-lg hover:text-gray-600" target="_blank" href={`https://www.ebay-kleinanzeigen.de/s-bestandsliste.html?userId=${productPage.seller_klaz_user_id}`}>
                    {/* <BiUserCircle className="w-8 h-8"/> */}
                    {productPage.seller_name}
                </a>

                <div className="mt-3 text-gray-500 text-sm">
                    <div className="flex flex-row gap-2 items-center">
                        {/* <GoLocation className=""/> */}
                        <div>
                            {productPage.postal_code} {productPage.city}
                        </div>
                    </div>
                </div>

                <div className="mt-6">
                    <a href={productPage.klaz_url} target="_blank"
                       className="rounded-full bg-main-green-bg py-2.5 px-3.5 text-sm font-semibold text-main-dark-green shadow-sm hover:bg-main-green-bg-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-main-green-bg">
                        Zur Anzeige
                    </a>
                </div>

            </div>

            <div className="bg-white mt-3 py-4 px-4">
                <div className="text-main-black-title font-black flex flex-row gap-3 items-center">
                    Beschreibung
                </div>
                <div className="border-0 border-gray-300 border-t py-4 mt-3 text-sm">
                    {productPage.description &&
                        productPage.description.split(/\r?\n/).map((block, index) => (
                            <p key={index} className="min-h-[1.15rem]">
                                {(block && block.trim() !== '') ? block : ''}
                            </p>
                        ))
                    }
                </div>
            </div>
        </div>
    );
};
