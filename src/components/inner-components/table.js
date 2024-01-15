import React from 'react';

const Table = ({
    checkbox, dataLoading,
    orderHeader, orderList,
    bulkCheckbox, setBulkCheckbox,
    handleCheckboxChange, setShowPopup,
    tableRef, handleScroll, pageComponent
}) => {


    return (
        <>
            <div
                ref={tableRef}
                className="wcip-block wcip-h-[400px] wcip-overflow-y-auto wcip-mt-8"
                onScroll={handleScroll}
            >
                <table className="wcip-table-auto wcip-bg-white wcip-text-[#3c434a] wcip-text-sm wcip-w-full wcip-mt-4 wcip-border wcip-border-[#c3c4c7]">

                    <thead key="topHeader" className="wcip-sticky wcip-top-0 wcip-bg-white wcip-border-b wcip-border-[#c3c4c7] wcip-z-[999]">
                        <tr key="topHeaderRow" className="wcip-p-1 wcip-border-b wcip-border-[#c3c4c7]">
                            <td key="topCB" id="cb" className="wcip-w-px wcip-p-2">
                                <input
                                    type="checkbox"
                                    onChange={() => setBulkCheckbox(bulkCheckbox ? false : true)}
                                    checked={bulkCheckbox ? true : false}
                                />
                            </td>
                            {Object.keys(orderHeader).map((header, index) => (
                                !(
                                    (header === 'remarks' && pageComponent !== 'Ship') ||
                                    (header === 'articleId' && pageComponent === 'Ship')
                                ) &&
                                <th key={index} className="wcip-w-[20ch] wcip-text-left wcip-p-2 wcip-font-normal"> {orderHeader[header]} </th>
                            ))}
                        </tr>
                    </thead>

                    <tbody className="wcip-break-words wcip-z-0">
                        {orderList ?
                            orderList.map((order, index) => {
                                return (
                                    <tr key={index} className="wcip-p-1 odd:wcip-bg-[#f0f0f1]">
                                        <td key="dataCB" id="cb" className="wcip-w-px wcip-p-2">
                                            <input key={index} id={index}
                                                type="checkbox"
                                                onChange={() => {
                                                    handleCheckboxChange(index, order.orderId)
                                                }}
                                                checked={(bulkCheckbox || checkbox[index][order.orderId]) ? true : false}
                                            />
                                        </td>
                                        {
                                            Object.keys(orderHeader).map((key, index) => (
                                                !(
                                                    (key === 'remarks' && pageComponent !== 'Ship') ||
                                                    (key === 'articleId' && pageComponent === 'Ship')
                                                ) &&
                                                <td key={index} className="wcip-w-[20ch] wcip-text-left wcip-p-3 wcip-font-normal">
                                                    {'remarks' === key ?
                                                        order[key] === '-' ?
                                                            order[key] :
                                                            <>
                                                                <p className="wcip-text-[#D63638] wcip-cursor-pointer" onClick={() => setShowPopup({ status: true, message: order[key] })}>
                                                                    Error
                                                                </p>
                                                            </>
                                                        :
                                                        <>
                                                            <a
                                                                className="wcip-text-[#2271b1] wcip-font-medium wcip-text-[13px]"
                                                                href={'/wp-admin/post.php?post=' + order.orderId + '&action=edit'}
                                                                target='_blank'>
                                                                {order[key] ? order[key] : '-'}
                                                            </a>
                                                        </>

                                                    }
                                                </td>
                                            ))
                                        }
                                    </tr>
                                )
                            })
                            :
                            <>
                                <tr key={'no-row'} className="wcip-p-1 odd:wcip-bg-[#f0f0f1]">
                                    <td colSpan={Object.keys(orderHeader).length + 1} className="wcip-w-px wcip-p-2 wcip-text-center"> No Records Found </td>
                                </tr>
                            </>
                        }
                        {dataLoading &&
                            <tr key={'last'} className="wcip-p-1 odd:wcip-bg-[#f0f0f1]">
                                <td colSpan={Object.keys(orderHeader).length + 1} className="wcip-text-center"> Loading more items.....</td>
                            </tr>
                        }
                    </tbody>


                    <thead key="bottomHeader" className="wcip-sticky wcip-bottom-0 wcip-bg-white wcip-border-b wcip-border-[#c3c4c7] wcip-z-[999]">
                        <tr key="bottomHeaderRow" className="wcip-p-1 wcip-border-t wcip-border-[#c3c4c7]">
                            <td key="bottomCB" id="cb" className="wcip-w-px wcip-p-2">
                                <input
                                    type="checkbox"
                                    onChange={() => setBulkCheckbox(bulkCheckbox ? false : true)}
                                    checked={bulkCheckbox ? true : false}
                                />
                            </td>
                            {Object.keys(orderHeader).map((header, index) => (
                                !(
                                    (header === 'remarks' && pageComponent !== 'Ship') ||
                                    (header === 'articleId' && pageComponent === 'Ship')
                                ) &&
                                <th key={index} className="wcip-w-[20ch] wcip-text-left wcip-p-2 wcip-font-normal"> {orderHeader[header]} </th>
                            ))}
                        </tr>
                    </thead>

                </table>
            </div>
        </>
    )
}

export default Table;