import React from "react";

export default (props) => {

    const {formattedPrice, formattedFees, formattedShipping, formattedAmount} = props;

    return (
        <div>
            <div className="flex flex-row gap-12 items-center w-full flex-grow">
                <div className="w-32 block text-sm text-main-black-title font-medium">
                    Preis
                </div>
                <div className="w-full text-sm">
                    {formattedPrice}
                </div>
            </div>
            <div className="flex flex-row gap-12 items-center w-full flex-grow mt-2">
                <div className="w-32 block text-sm text-main-black-title font-medium">
                    Gebühren
                </div>
                <div className="w-full text-sm">
                    {formattedFees}
                </div>
            </div>
            <div className="flex flex-row gap-12 items-center w-full flex-grow mt-2">
                <div className="w-32 block text-sm text-main-black-title font-medium">
                    Versand
                </div>
                <div className="w-full text-sm">
                    {formattedShipping}
                </div>
            </div>
            <div className="flex flex-row gap-12 items-center w-full flex-grow mt-2">
                <div className="w-32 block text-sm text-main-black-title font-semibold">
                    Summe
                </div>
                <div className="w-full text-sm font-semibold">
                    {formattedAmount}
                </div>
            </div>
        </div>
    );
};
