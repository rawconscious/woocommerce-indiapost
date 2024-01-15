import React, {
    useState,
    useEffect,
    useRef,
} from 'react';
import {
    getDefaultStatus,
    registerDefaultStatus,
    getOrderList,
    updateOrderStatus
} from '../../utils/rest-api-orders';
import { cancelOrder, sendReuest } from '../../utils/rc-api-indiapost';
import Button from './buttons';
import Loading from './loading';
import Table from './table';

const StatusComponent = ({ setNotice, pageComponent }) => {

    const [checkbox, setCheckbox] = useState([]);
    const [orderHeader, setOrderHeader] = useState(['']);
    const [orderList, setOrderList] = useState(0);
    const [bulkCheckbox, setBulkCheckbox] = useState(false);
    const [defaultShip, setDefaultShip] = useState(null);
    const [showPopup, setShowPopup] = useState({
        status: false,
        message: ''
    });
    const [filterStatus, setFilterStatus] = useState('all-date');
    const [hasMore, setHasMore] = useState(true);
    const [loading, setLoading] = useState(false);
    const [dataLoading, setDataLoading] = useState(false);

    const tableRef = useRef(null);

    // Date filter options.
    const filterOptions = [
        { value: "all-date", label: "All Date" },
        { value: "today", label: "Today" },
        { value: "last-3-days", label: "Last 3 Days" },
        { value: "this-week", label: "This Week" },
    ];


    // Function to handle table checkbox.
    const checkboxItem = (orderList) => {
        if (orderList) {
            let newCheckbox = [...checkbox];
            orderList.map((order) => (
                newCheckbox = [...newCheckbox, { [order.orderId]: false }]
            ));
            setCheckbox(newCheckbox);
        }
    }

    // Function to fetch order list.
    const fetchOrderList = async (statusChange = false) => {

        let orderCount = 0;

        if (!statusChange && (0 !== orderList)) {
            orderCount = orderList.length;
        } else {
            setLoading(true);
        }
        const orderStatus = pageComponent.replace(/([A-Z])/g, "-$1").toLowerCase().substring(1);
        const list = await getOrderList(orderStatus, filterStatus, orderCount);

        if (list.isSuccess) {
            let result = list.results;
            let header = result.header;
            let data = result.data;
            let newOrderList = (0 === orderCount) ? data : [...orderList, ...data];
            let hasMoreState = list.hasMore;

            checkboxItem(newOrderList);
            setOrderHeader(header);
            setOrderList(newOrderList);
            setHasMore(hasMoreState);
            setDataLoading(false);
            setLoading(false);
        } else {
            let result = list.results;
            let header = result.header;
            (0 === orderCount) && setOrderList(null);
            setOrderHeader(header);
            setHasMore(false);
            setLoading(false);
        }
    };

    // Function to handle checkbox change.
    const handleCheckboxChange = (position, orderId) => {
        let newCheckBoxState = [...checkbox];
        newCheckBoxState[position] = checkbox[position][orderId] ? { [orderId]: false } : { [orderId]: true };
        setCheckbox(newCheckBoxState);
    }

    // Function to handle send order request.
    const sendOrderRequest = async (requestData) => {

        setLoading(true);

        let requestType = ('Dropoff' === defaultShip) ? 'booking' : 'pickup';
        const requestStatus = await sendReuest(requestData, requestType);

        if (requestStatus.isSuccess) {
            let statusType = requestStatus.statusType ? requestStatus.statusType : 'success';
            let updateMessage = requestStatus.message ? requestStatus.message : 'Updated Successfully';

            setOrderList(null);
            fetchOrderList(true);
            setNotice({ status: statusType, message: updateMessage });
            setBulkCheckbox(false);
            setLoading(false);

        } else {
            let updateMessage = requestStatus.message ? requestStatus.message : 'Something went wrong';
            setNotice({ status: 'error', message: updateMessage });
            setBulkCheckbox(false);
            setLoading(false);
            fetchOrderList(true);
        }
    }

    // Function to handle send order request.
    const sendCancelRequest = async (orderIds) => {

        setLoading(true);

        const requestStatus = await cancelOrder(orderIds);

        if (requestStatus.isSuccess) {
            let statusType = requestStatus.statusType ? requestStatus.statusType : 'success';
            let updateMessage = requestStatus.message ? requestStatus.message : 'Order Cancelled Successfully';

            setOrderList(null);
            fetchOrderList(true);
            setNotice({ status: statusType, message: updateMessage });
            setBulkCheckbox(false);
            setLoading(false);

        } else {
            let updateMessage = requestStatus.message ? requestStatus.message : 'Something went wrong';
            setNotice({ status: 'error', message: updateMessage });
            setBulkCheckbox(false);
            setLoading(false);
            fetchOrderList(true);
        }
    }

    // Change Order Status.
    const changeOrderStatus = async (orderIds, orderStatus) => {
        setLoading(true);
        const updateResult = await updateOrderStatus(orderIds, orderStatus);

        if (updateResult.isSuccess) {
            let statusType = updateResult.statusType;
            let message = updateResult.message;

            setNotice({ status: statusType, message: message });
            fetchOrderList(true);
            setLoading(false);
        } else {
            setNotice({ status: 'error', message: 'something went wrong' });
            setLoading(false);
        }
    }

    // Function to handle ship order ids.
    const handleShip = (shipFlag = false) => {
        const orderIds = [];
        if (true === bulkCheckbox || true === shipFlag) {
            Object.values(orderList).forEach((orders) => {
                orderIds.push(orders.orderId);
            });
        } else {
            checkbox.forEach(order => {
                Object.keys(order).forEach(orderId => {
                    if (true === order[orderId]) {
                        orderIds.push(parseInt(orderId));
                    }
                });
            });
        }

        if ('Ship' === pageComponent) {
            sendOrderRequest(orderIds);
        } else if ('Dropoff' === pageComponent) {
            changeOrderStatus(orderIds, 'dropoff-done');
        } else if ('Pickup' === pageComponent) {
            changeOrderStatus(orderIds, 'pickup-done');
        } else if ('DropoffDone' === pageComponent) {
            changeOrderStatus(orderIds, 'dropoff');
        } else {
            changeOrderStatus(orderIds, 'pickup');
        }
    }

    // Function to hnadle cancel.
    const handleCancel = (shipFlag = false) => {
        const orderIds = [];
        if (true === bulkCheckbox || true === shipFlag) {
            Object.values(orderList).forEach((orders) => {
                orderIds.push(orders.orderId);
            });
        } else {
            checkbox.forEach(order => {
                Object.keys(order).forEach(orderId => {
                    if (true === order[orderId]) {
                        orderIds.push(parseInt(orderId));
                    }
                });
            });
        }
        sendCancelRequest(orderIds);
    }

    // Function to get default status.
    const fetchDefaultStatus = async () => {
        const results = await getDefaultStatus();

        if (results.isSuccess) {
            setDefaultShip(results.status);
        } else {
            setDefaultShip('Dropoff')
        }
    }

    // Function to update default status.
    const updateDefaultStatus = async () => {
        const results = await registerDefaultStatus(defaultShip);

    }

    const handleScroll = () => {

        if (hasMore) {
            setDataLoading(true);
            fetchOrderList();
        }

    }

    useEffect(() => {
        fetchDefaultStatus();

        // fetchOrderList();
    }, []);

    useEffect(() => {
        fetchOrderList(true);
    }, [pageComponent]);


    useEffect(() => {
        defaultShip && updateDefaultStatus();
    }, [defaultShip]);


    if (0 === orderList) {
        return (
            <>
                <Loading />
            </>
        );
    } else {
        return (
            <>
                {loading
                    ? <Loading />
                    : <div className='wcip-mt-8 wcip-container wcip-mx-auto wcip-p-2 wcip-w-[100%] wcip-text-sm'>
                        <Button
                            setBulkCheckbox={setBulkCheckbox}
                            handleShip={handleShip}
                            handleCancel={handleCancel}
                            defaultShip={defaultShip}
                            setDefaultShip={setDefaultShip}
                            filterOptions={filterOptions}
                            filterStatus={filterStatus}
                            setFilterStatus={setFilterStatus}
                            fetchOrderList={fetchOrderList}
                            pageComponent={pageComponent}
                        />

                        <Table
                            checkbox={checkbox}
                            orderHeader={orderHeader}
                            orderList={orderList}
                            bulkCheckbox={bulkCheckbox}
                            setBulkCheckbox={setBulkCheckbox}
                            handleCheckboxChange={handleCheckboxChange}
                            setShowPopup={setShowPopup}
                            dataLoading={dataLoading}
                            tableRef={tableRef}
                            handleScroll={handleScroll}
                            pageComponent={pageComponent}
                        />

                    </div>
                }
                {showPopup.status &&
                    <div className="wcip-fixed wcip-top-0 wcip-right-0 wcip-bottom-0 wcip-left-0 wcip-p-4 wcip-h-max wcip-w-max wcip-m-auto wcip-max-w-[75%] wcip-bg-white wcip-border wcip-border-[#c3c4c7] wcip-z-[999]">
                        <div className='wcip-flex wcip-justify-between wcip-gap-4'>
                            <p className='wcip-mt-2'> {showPopup.message} </p>
                            <button className="wcip-p-2" onClick={() => setShowPopup({ status: false, message: '' })}>
                                <span className="wcip-rounded-[50%] wcip-px-1 wcip-py-px wcip-text-white wcip-text-center wcip-bg-[#787c82] hover:wcip-bg-[#d63638] wcip-text-[10px] wcip-my-auto"> X </span>
                            </button>
                        </div>
                    </div>
                }
            </>
        );
    }
}

export default StatusComponent;