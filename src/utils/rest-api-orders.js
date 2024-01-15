import apiFetch from '@wordpress/api-fetch';

const getOrderList = async (orderStatus, filterStatus, offset) => {

    return await apiFetch({ path: 'rc-wcip/v1/order/get-list/' + orderStatus + '/' + filterStatus + '/' + offset }).then((orderList) => {
        return orderList;
    });
}

const getDefaultStatus = async () => {
    return await apiFetch({ path: 'rc-wcip/v1/get-default-status' }).then((defaultStatus) => {
        return defaultStatus;
    });
}

const registerDefaultStatus = async (status) => {
    return await apiFetch({ path: 'rc-wcip/v1/register-default-status/' + status }).then((defaultStatus) => {
        return defaultStatus;
    })
}

const updateOrderStatus = async (orderIds, orderStatus) => {

    return await apiFetch({
        path: 'rc-wcip/v1/update-order-status/' + orderStatus,
        method: 'POST',
        data: {
            orderIds: orderIds,
        }
    }).then((updateResult) => {
        return updateResult;
    })
}

export { getOrderList, getDefaultStatus, registerDefaultStatus, updateOrderStatus };