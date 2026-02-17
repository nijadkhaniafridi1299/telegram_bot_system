import React from "react";
import headerDesk from '../images/header-logo-new.svg';

export default () => {
    return (
        <div className="pt-5 pb-5">
            <div className="flex flex-col gap-1 justify-start">
                <img src={headerDesk} className="max-w-[14rem]" />
            </div>
        </div>
    );
};
