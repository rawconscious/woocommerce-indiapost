import React from "react";

const Loading = () => {

    return (
        <>
            <div className='wcip-absolute wcip-flex wcip-flex-col wcip-gap-4 wcip-justify-center wcip-items-center wcip-w-full wcip-h-full wcip-inset-0 wcip-z-[999]'>
                <div className='wcip-animate-spin wcip-h-16 wcip-w-16 wcip-border-4 wcip-rounded-full  wcip-border-x-[#2271b1]'>
                </div>
                <h3 className="wcip-text-md wcip-text-[#1d2327] wcip-font-semibold"> Loading </h3>
            </div>
        </>
    );
}

export default Loading;