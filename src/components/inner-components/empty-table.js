import React from "react";

const EmptyTable = ({ orderHeader }) => {

    return (
        <>
            <table className="wcip-table-auto wcip-bg-white wcip-text-[#3c434a] wcip-text-sm wcip-w-full wcip-mt-4 wcip-border wcip-border-[#c3c4c7]">

                <thead>
                    <tr className="wcip-p-1 wcip-border-b wcip-border-[#c3c4c7]">
                        <td id="cb" className="wcip-w-px wcip-p-2">
                            <input
                                type="checkbox"
                                checked={false}
                            />
                        </td>
                        {Object.keys(orderHeader).map((header, index) => (
                            <th key={index} className="wcip-w-[20ch] wcip-text-left wcip-p-2 wcip-font-normal"> {orderHeader[header]} </th>
                        ))}
                        <th className="wcip-w-[20ch] wcip-text-left wcip-p-2 wcip-font-normal"> </th>
                    </tr>
                </thead>

                <tbody className='wcip-break-words'>
                    <tr className='wcip-border-t wcip-border-[#f5f5f5] odd:wcip-bg-[#f6f7f7]'>
                        <td id="cb" className="wcip-w-px wcip-p-2" colSpan={5}>
                            No Records Found
                        </td>
                    </tr>
                </tbody>

                <thead>
                    <tr className="wcip-p-1 wcip-border-b wcip-border-[#c3c4c7]">
                        <td id="cb" className="wcip-w-px wcip-p-2">
                            <input
                                type="checkbox"
                                checked={false}
                            />
                        </td>
                        {Object.keys(orderHeader).map((header, index) => (
                            <th key={index} className="wcip-w-[20ch] wcip-text-left wcip-p-2 wcip-font-normal"> {orderHeader[header]} </th>
                        ))}
                        <th className="wcip-w-[20ch] wcip-text-left wcip-p-2 wcip-font-normal">  </th>
                    </tr>
                </thead>

            </table>
        </>
    );
}

export default EmptyTable;