import React, {
    useState,
} from "react";

const Notice = ({ notice, setNotice }) => {

    let outerClassName = "wcip-my-4 wcip-relative wcip-p-2 wcip-pr-10 wcip-bg-white wcip-border wcip-border-[#c3c4c7] wcip-border-l-4";
    if ('error' === notice.status) {
        outerClassName = outerClassName + " wcip-border-l-[#d63638]";
    } else if ('success' === notice.status ) {
        outerClassName = outerClassName + " wcip-border-l-[#00a32a]"
    } else {
        outerClassName = outerClassName + " wcip-border-l-yellow-500"
    }

    return (
        <>
            {notice.message ?
                <div className={outerClassName}>
                    <p className="wcip-text-[#3c434a]"> {notice.message} </p>
                    <button className="wcip-absolute wcip-top-0 wcip-right-2 wcip-p-2" onClick={() => setNotice({ status: null, message: null })}>
                        <span className="wcip-rounded-[50%] wcip-px-1 wcip-py-px wcip-text-white wcip-text-center wcip-bg-[#787c82] hover:wcip-bg-[#d63638] wcip-text-[10px] wcip-my-auto"> X </span>
                    </button>
                </div>
                :
                <></>
            }
        </>
    )
}

export default Notice;