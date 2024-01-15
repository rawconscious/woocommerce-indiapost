import apiFetch from '@wordpress/api-fetch';

const sendReuest = async (orderIds, requestType) => {

    return await apiFetch({
        path: 'rc-wcip/v1/india-post-booking-request/' + requestType,
        method: 'POST',
        data: {
            orderIds: orderIds
        }
    }).then((requestStatus) => {
        return requestStatus;
    });
}

const cancelOrder = async (orderIds) => {

    return await apiFetch({
        path: 'rc-wcip/v1/india-post-cancel-request/',
        method: 'POST',
        data: {
            orderIds: orderIds
        }
    }).then((requestStatus) => {
        return requestStatus;
    });
}

export { sendReuest, cancelOrder };