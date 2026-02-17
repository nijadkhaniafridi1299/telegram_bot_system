import React from "react";
import vp_cc from "../images/vp_kreditkarte.png";
import vp_safe from "../images/vp_safe.png";

export default () => {
    return (
        <div className="mt-12 bg-main-white pb-12">
            <div className="flex flex-row items-center justify-between">
                <div className="flex flex-col items-center justify-center max-w-[28rem]">
                    <img src={vp_cc} className=""/>
                    <div className="text-main-black-title font-black text-xl text-center">
                        Schnell und unkompliziert
                    </div>
                    <div className="mt-3 text-main-black-title text-sm px-5 text-center">
                        Unser Bezahlservice macht das Handeln noch einfacher - bezahle per Überweisung über unsere
                        Seite.
                    </div>
                </div>
                <div className="flex flex-col items-center justify-center max-w-[28rem]">
                    <img src={vp_safe} className=""/>
                    <div className="text-main-black-title font-black text-xl text-center">
                        Immer abgesichert
                    </div>
                    <div className="mt-3 text-main-black-title text-sm px-5 text-center">
                        Ist für den Käufer alles in Ordnung, wird das Geld freigegeben. Ist das Produkt nicht wie
                        beschrieben, erstatten wir den Betrag.
                    </div>
                </div>
            </div>
        </div>
    );
};
