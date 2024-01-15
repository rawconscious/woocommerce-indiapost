import React from "react";

const Button = ({
    setBulkCheckbox, handleShip, handleCancel,
    defaultShip, setDefaultShip,
    filterOptions, filterStatus, setFilterStatus,
    fetchOrderList, pageComponent,
}) => {

    const buttonClass = "wcip-bg-white wcip-border wcip-border-[#0a4b78] wcip-px-2.5 wcip-py-1 wcip-text-[#0a4b78] wcip-rounded-[3px]";
    return (
        <>
            <div id="button-class" className='wcip-flex wcip-flex-wrap wcip-items-center wcip-gap-5'>
                {'Ship' === pageComponent &&
                    <>
                        <button
                            className={buttonClass}
                            onClick={() => {
                                handleShip(false);
                            }
                            }
                        >
                            Book Selected
                        </button>

                        <button
                            className={buttonClass}
                            onClick={() => {
                                setBulkCheckbox(true);
                                handleShip(true);
                            }}>
                            Book All
                        </button>

                        <div className="wcip-flex wcip-flex-col max-xl:wcip-text-sm">

                            {defaultShip &&
                                <div className="wcip-flex wcip-m-px wcip-items-center wcip-gap-x-2">
                                    Drop off
                                    <div
                                        className="wcip-w-8 wcip-h-3 wcip-flex wcip-items-center wcip-bg-gray-400 wcip-rounded-full wcip-p-1 wcip-cursor-pointer"
                                        onClick={() => {
                                            let newdefaultShip = defaultShip === 'Dropoff' ? 'Pickup' : 'Dropoff';
                                            setDefaultShip(newdefaultShip);
                                        }}
                                    >
                                        <div
                                            className={
                                                "wcip-bg-blue-500 md:wcip-w-4 md:wcip-h-4 wcip-ml-[-4px] wcip-h-3 wcip-w-3 wcip-rounded-full wcip-shadow-md wcip-transform wcip-duration-300 wcip-ease-in-out" +
                                                ('Dropoff' === defaultShip ? null : 'wcip-transform wcip-translate-x-4')
                                            }
                                        ></div>
                                    </div>
                                    Pickup
                                </div>

                            }
                        </div>
                    </>
                }
                {('Dropoff' === pageComponent || 'Pickup' === pageComponent) &&
                    <>
                        <button
                            className={buttonClass}
                            onClick={() => {
                                handleShip();
                            }}
                        >
                            Complete Selected
                        </button>
                        <button
                            className={buttonClass}
                            onClick={() => {
                                setBulkCheckbox(true);
                                handleShip(true);
                            }}
                        >
                            Complete All
                        </button>
                        <button
                            className={buttonClass}
                            onClick={() => {
                                handleCancel();
                            }}
                        >
                            Cancel Selected
                        </button>
                        <button
                            className={buttonClass}
                            onClick={() => {
                                setBulkCheckbox(true);
                                handleCancel(true);
                            }}
                        >
                            Cancel All
                        </button>
                    </>
                }
                {('DropoffDone' === pageComponent || 'PickupDone' === pageComponent) &&
                    <>
                        <button
                            className={buttonClass}
                            onClick={() => {
                                handleShip();
                            }}
                        >
                            Incomplete Selected
                        </button>
                        <button
                            className={buttonClass}
                            onClick={() => {
                                setBulkCheckbox(true);
                                handleShip(true);
                            }}
                        >
                            Incomplete All
                        </button>
                    </>
                }
                <div id="filter" className="wcip-flex wcip-gap-2">
                    <select
                        id="dateFilter"
                        value={filterStatus}
                        onChange={(event) => setFilterStatus(event.target.value)}
                    >
                        {filterOptions.map((options, index) => {
                            return (
                                <option key={'filter-' + index} value={options.value} > {options.label} </option>
                            )
                        }
                        )}
                    </select>

                    <button
                        id="filter"
                        className={buttonClass}
                        onClick={() => {
                            fetchOrderList(true);
                        }
                        }
                    >
                        Filter
                    </button>

                </div>
            </div>
        </>
    )

}

export default Button;