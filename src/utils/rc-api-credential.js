import apiFetch from '@wordpress/api-fetch';


const saveCredentials = async (credentials, address) => {

    return await apiFetch({
        path: 'rc-wcip/v1/credentials/save-credentials',
        method: 'POST',
        data: {
            'credentials': credentials,
            'address': address,
        }
    }).then((credentialsResult) => {
        return credentialsResult;
    });
}

const getCredentials = async () => {

    return await apiFetch({ path: 'rc-wcip/v1/credentials/get-credentials' }).then((credentials) => {
        return credentials;
    });
}

export { saveCredentials, getCredentials };