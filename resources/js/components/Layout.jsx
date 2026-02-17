import React from "react";
import Footer from "./Footer";
import Header from "./Header";
// import {TbShieldCheck} from "react-icons";

export default (props) => {
    return (
        <div>
            <div className="max-w-4xl mx-4 lg:mx-auto">
                <Header/>
            </div>
            <div className="bg-main-green-bg py-2">
                <div className="max-w-4xl mx-auto">
                    <div className="flex flex-row gap-2 justify-center items-center w-full">
                        {/* <TbShieldCheck className="text-main-dark-green w-6 h-6"/> */}
                        <div className="text-main-dark-green text-lg">
                            Unser Käuferschutz sichert dich ab
                        </div>
                    </div>
                </div>
            </div>
            <div className="max-w-4xl mx-auto">

                {props.children}

            </div>
            <div className="max-w-4xl mx-auto">
                <Footer/>
            </div>
        </div>
    );
};
