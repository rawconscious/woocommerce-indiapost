import React, {
    useState,
    useEffect
} from 'react';
import { set, useForm } from 'react-hook-form';
import {
    getCredentials,
    saveCredentials,
} from '../../utils/rc-api-credential';
import {
getArtilceIdsCount,
importArticleIds,
} from '../../utils/rc-api-articleid'
import Loading from './loading';

const Config = ({ setNotice }) => {
    const [loading, setLoading] = useState(false);
    const [credentials, setCredentials] = useState({
        username: '',
        password: '',
        identifier: '',
        contractId: '',
    });
    const [warehouseAddress, setWarehouseAddress] = useState({
        senderName: '',
        senderEmail: '',
        senderPhone: '',
        address1: '',
        address2: '',
        city: '',
        state: '',
        pincode: '',
    });

    const [articleId, setArticleId] = useState({
        type: '',
        file: '',
    });
    const [articleIdCount, setArticleIdCount] = useState(0);
    const [popup, setPopup] = useState(false);

    const { register, handleSubmit, formState: { errors }, reset } = useForm({ defaultValues: credentials });
    const {
        register: register2,
        formState: { errors: errors2 },
        handleSubmit: handleSubmit2,
    } = useForm({
        mode: "onBlur",
    });


    // Fetch credentials
    const fetchCredentials = async () => {
        setLoading(true);
        const results = await getCredentials();
        if (results.isSuccess) {
            const { username, password, identifier, contractId, address } = results.credentials;

            if (address) {
                let addressResult = JSON.parse(address);

                let senderName = addressResult.senderName;
                let senderEmail = addressResult.senderEmail;
                let senderPhone = addressResult.senderPhone;
                let address1 = addressResult.address1;
                let address2 = addressResult.address2;
                let city = addressResult.city;
                let state = addressResult.state;
                let pincode = addressResult.pincode;

                setWarehouseAddress({
                    senderName: senderName,
                    senderEmail: senderEmail,
                    senderPhone: senderPhone,
                    address1: address1,
                    address2: address2,
                    city: city,
                    state: state,
                    pincode: pincode,
                });
            }

            setCredentials({
                username: username,
                password: password,
                identifier: identifier,
                contractId: contractId,
            });
            reset(results.credentials);
            setLoading(false);
        } else {
            setLoading(false);
        }
    };

    // Fetch article ID count
    const fetchArticleIdsCount = async () => {
        const results = await getArtilceIdsCount();
        setArticleIdCount(results.isSuccess ? results.count : 0);
    };

    // Add/update credentials
    const addCredentials = async () => {
        setLoading(true);
        const credentialsAdd = await saveCredentials(credentials, JSON.stringify(warehouseAddress));
        setLoading(false);

        if (credentialsAdd.isSuccess) {
            setNotice({ status: 'success', message: 'Credential Saved Successfully' });
        } else {
            setNotice({ status: 'error', message: 'Oops!... Credential Not Updated' });
        }
    };

    // Handle form submission
    const handleForm = () => {
        addCredentials();
    };

    // Import article IDs
    const addArticleIds = async () => {
        setLoading(true);
        const result = await importArticleIds(articleId.type, articleId.file);
        setLoading(false);

        if (result.isSuccess) {
            let message = result.message ? result.message : 'Imported Successfully';
            setNotice({ status: 'success', message: message });
            setArticleIdCount(0);
            fetchArticleIdsCount();
        } else {
            let message = result.message ? result.message : 'Something Went Wrong';
            setNotice({ status: 'error', message: message });
        }
    };

    // Handle article IDs import
    const handleArticleIds = () => {
        ('append' === articleId.type || 'overwrite' === articleId.type) && addArticleIds();
    };

    // Handles Key Down Event
    const handleKeydown = (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    };

    useEffect(() => {
        fetchCredentials();
        fetchArticleIdsCount();
    }, []);


    if (loading) {
        return (
            <>
                <Loading />
            </>
        )
    } else {
        return (
            <>

                <div className="wcip-px-3 wcip-text-sm wcip-mt-8">

                    <h2 className="wcip-text-xl wcip-text-[#1d2327] wcip-my-1 wcip-font-semibold">
                        Credintials
                    </h2>

                    <p className="wcip-text-[#3c434a]"> Add Your Credintials </p>

                    <form id="credentials" key={1} onSubmit={handleSubmit(handleForm)} className="wcip-grid md:wcip-grid-cols-2 wcip-mt-8 wcip-gap-5">

                        <div id="credentials-left" className="wcip-grid wcip-gap-5">

                            <div id="sendername" className="wcip-flex wcip-gap-5 wcip-items-center">

                                <p className="wcip-font-semibold wcip-text-[#1d2327] wcip-w-[20%]"> Sender Name </p>

                                <input
                                    type="text"
                                    className="wcip-w-96 wcip-rounded-[4px] wcip-bg-white wcip-border wcip-border-[#8c8f94] wcip-text-[#2c3338] wcip-min-h-[30px]"
                                    value={warehouseAddress.senderName}
                                    {...register('senderName', { required: true })}
                                    onChange={(event) => setWarehouseAddress({ ...warehouseAddress, senderName: event.target.value })}
                                    onKeyDown={handleKeydown}
                                />

                                {errors.senderName &&
                                    (errors.senderName.type === 'required' &&
                                        <span className='wcip-text-red-600'>
                                            This field is required*.
                                        </span>
                                    )
                                }

                            </div>

                            <div id="senderEmail" className="wcip-flex wcip-gap-5 wcip-items-center">

                                <p className="wcip-font-semibold wcip-text-[#1d2327] wcip-w-[20%]"> Sender Email </p>

                                <input
                                    type="email"
                                    className="wcip-w-96 wcip-rounded-[4px] wcip-bg-white wcip-border wcip-border-[#8c8f94] wcip-text-[#2c3338] wcip-min-h-[30px]"
                                    value={warehouseAddress.senderEmail}
                                    {...register('senderEmail', { required: true })}
                                    onChange={(event) => setWarehouseAddress({ ...warehouseAddress, senderEmail: event.target.value })}
                                    onKeyDown={handleKeydown}
                                />

                                {errors.senderEmail &&
                                    (errors.senderEmail.type === 'required' &&
                                        <span className='wcip-text-red-600'>
                                            This field is required*.
                                        </span>
                                    )
                                }

                            </div>

                            <div id="senderPhone" className="wcip-flex wcip-gap-5 wcip-items-center">

                                <p className="wcip-font-semibold wcip-text-[#1d2327] wcip-w-[20%]"> Sender Phone </p>

                                <input
                                    type="tel"
                                    pattern="[7-9]{1}[0-9]{9}"
                                    className="wcip-w-96 wcip-rounded-[4px] wcip-bg-white wcip-border wcip-border-[#8c8f94] wcip-text-[#2c3338] wcip-min-h-[30px]"
                                    value={warehouseAddress.senderPhone}
                                    {...register('senderPhone', { required: true })}
                                    onChange={(event) => setWarehouseAddress({ ...warehouseAddress, senderPhone: event.target.value })}
                                    onKeyDown={handleKeydown}
                                />

                                {errors.senderPhone &&
                                    (errors.senderPhone.type === 'required' &&
                                        <span className='wcip-text-red-600'>
                                            This field is required*.
                                        </span>
                                    )
                                }

                            </div>

                            <div id="username" className="wcip-flex wcip-gap-5 wcip-items-center">

                                <p className="wcip-font-semibold wcip-text-[#1d2327] wcip-w-[20%]"> Username </p>

                                <input
                                    type="number"
                                    pattern="[0-9]*"
                                    min={0}
                                    className="wcip-w-96 wcip-rounded-[4px] wcip-bg-white wcip-border wcip-border-[#8c8f94] wcip-text-[#2c3338] wcip-min-h-[30px]"
                                    value={credentials.username}
                                    {...register('username', { required: true })}
                                    onChange={(event) => setCredentials({ ...credentials, username: event.target.value })}
                                    onKeyDown={handleKeydown}
                                />

                                {errors.username &&
                                    (errors.username.type === 'required' &&
                                        <span className='wcip-text-red-600'>
                                            This field is required*.
                                        </span>
                                    )
                                }

                            </div>

                            <div id="password" className="wcip-flex wcip-gap-5 wcip-items-center">

                                <p className="wcip-font-semibold wcip-text-[#1d2327] wcip-w-[20%]"> Password </p>

                                <input
                                    type="password"
                                    className="wcip-w-96 wcip-rounded-[4px] wcip-bg-white wcip-border wcip-border-[#8c8f94] wcip-text-[#2c3338] wcip-min-h-[30px]"
                                    value={credentials.password}
                                    {...register('password', { required: true })}
                                    onChange={(event) => setCredentials({
                                        ...credentials, password: event.target.value
                                    })}
                                    onKeyDown={handleKeydown}
                                />

                                {errors.password &&
                                    (errors.password.type === 'required' &&
                                        <span className='wcip-text-red-600'>
                                            This field is required*.
                                        </span>
                                    )
                                }

                            </div>

                            <div id="identifier" className="wcip-flex wcip-gap-5 wcip-items-center">

                                <p className="wcip-font-semibold wcip-text-[#1d2327] wcip-w-[20%]"> Identifier </p>

                                <input
                                    type="number"
                                    pattern="[0-9]*"
                                    min={0}
                                    className="wcip-w-96 wcip-rounded-[4px] wcip-bg-white wcip-border wcip-border-[#8c8f94] wcip-text-[#2c3338] wcip-min-h-[30px]"
                                    value={credentials.identifier}
                                    {...register('identifier', { required: true })}
                                    onChange={(event) => setCredentials({
                                        ...credentials, identifier: event.target.value
                                    })}
                                    onKeyDown={handleKeydown}
                                />

                                {errors.identifier &&
                                    (errors.identifier.type === 'required' &&
                                        <span className='wcip-text-red-600'>
                                            This field is required*.
                                        </span>
                                    )
                                }

                            </div>

                            <div id="contractId" className="wcip-flex wcip-gap-5 wcip-items-center">

                                <p className="wcip-font-semibold wcip-text-[#1d2327] wcip-w-[20%]"> Contract Id </p>

                                <input
                                    type="number"
                                    pattern="[0-9]*"
                                    min={0}
                                    className="wcip-w-96 wcip-rounded-[4px] wcip-bg-white wcip-border wcip-border-[#8c8f94] wcip-text-[#2c3338] wcip-min-h-[30px]"
                                    value={credentials.contractId}
                                    {...register('contractId', { required: true })}
                                    onChange={(event) => setCredentials({
                                        ...credentials, contractId: event.target.value
                                    })}
                                    onKeyDown={handleKeydown}
                                />

                                {errors.contractId &&
                                    (errors.contractId.type === 'required' &&
                                        <span className='wcip-text-red-600 wcip-ml-3'>
                                            This field is required*.
                                        </span>
                                    )
                                }

                            </div>

                        </div>

                        <div id="address" className="wcip-grid wcip-gap-5">

                            <div id="address1" className="wcip-flex wcip-gap-5 wcip-items-center">

                                <p className="wcip-font-semibold wcip-text-[#1d2327] wcip-w-[20%]"> Address Line 1 </p>

                                <input
                                    type="text"
                                    className="wcip-w-96 wcip-rounded-[4px] wcip-bg-white wcip-border wcip-border-[#8c8f94] wcip-text-[#2c3338] wcip-min-h-[30px]"
                                    value={warehouseAddress.address1}
                                    {...register('address1', { required: true })}
                                    onChange={(event) => setWarehouseAddress({
                                        ...warehouseAddress, address1: event.target.value
                                    })}
                                    onKeyDown={handleKeydown}
                                />

                                {errors.address1 &&
                                    (errors.address1.type === 'required' &&
                                        <span className='wcip-text-red-600 wcip-ml-3'>
                                            This field is required*.
                                        </span>
                                    )
                                }

                            </div>

                            <div id="address2" className="wcip-flex wcip-gap-5 wcip-items-center">

                                <p className="wcip-font-semibold wcip-text-[#1d2327] wcip-w-[20%]"> Address Line 2 </p>

                                <input
                                    type="text"
                                    className="wcip-w-96 wcip-rounded-[4px] wcip-bg-white wcip-border wcip-border-[#8c8f94] wcip-text-[#2c3338] wcip-min-h-[30px]"
                                    value={warehouseAddress.address2}
                                    {...register('address2', { required: true })}
                                    onChange={(event) => setWarehouseAddress({
                                        ...warehouseAddress, address2: event.target.value
                                    })}
                                    onKeyDown={handleKeydown}
                                />

                                {errors.address2 &&
                                    (errors.address2.type === 'required' &&
                                        <span className='wcip-text-red-600 wcip-ml-3'>
                                            This field is required*.
                                        </span>
                                    )
                                }

                            </div>

                            <div id="city" className="wcip-flex wcip-gap-5 wcip-items-center">

                                <p className="wcip-font-semibold wcip-text-[#1d2327] wcip-w-[20%]"> City </p>

                                <input
                                    type="text"
                                    className="wcip-w-96 wcip-rounded-[4px] wcip-bg-white wcip-border wcip-border-[#8c8f94] wcip-text-[#2c3338] wcip-min-h-[30px]"
                                    value={warehouseAddress.city}
                                    {...register('city', { required: true })}
                                    onChange={(event) => setWarehouseAddress({
                                        ...warehouseAddress, city: event.target.value
                                    })}
                                    onKeyDown={handleKeydown}
                                />

                                {errors.city &&
                                    (errors.city.type === 'required' &&
                                        <span className='wcip-text-red-600 wcip-ml-3'>
                                            This field is required*.
                                        </span>
                                    )
                                }

                            </div>

                            <div id="state" className="wcip-flex wcip-gap-5 wcip-items-center">

                                <p className="wcip-font-semibold wcip-text-[#1d2327] wcip-w-[20%]"> State </p>

                                <input
                                    type="text"
                                    className="wcip-w-96 wcip-rounded-[4px] wcip-bg-white wcip-border wcip-border-[#8c8f94] wcip-text-[#2c3338] wcip-min-h-[30px]"
                                    value={warehouseAddress.state}
                                    {...register('state', { required: true })}
                                    onChange={(event) => setWarehouseAddress({
                                        ...warehouseAddress, state: event.target.value
                                    })}
                                    onKeyDown={handleKeydown}
                                />

                                {errors.state &&
                                    (errors.state.type === 'required' &&
                                        <span className='wcip-text-red-600 wcip-ml-3'>
                                            This field is required*.
                                        </span>
                                    )
                                }

                            </div>

                            <div id="pincode" className="wcip-flex wcip-gap-5 wcip-items-center">

                                <p className="wcip-font-semibold wcip-text-[#1d2327] wcip-w-[20%]"> Pincode </p>

                                <input
                                    type="number"
                                    pattern="[0-9*]{6}"
                                    maxLength={6}
                                    min={0}
                                    className="wcip-w-96 wcip-rounded-[4px] wcip-bg-white wcip-border wcip-border-[#8c8f94] wcip-text-[#2c3338] wcip-min-h-[30px]"
                                    value={warehouseAddress.pincode}
                                    {...register('pincode', { required: true })}
                                    onChange={(event) => setWarehouseAddress({
                                        ...warehouseAddress, pincode: event.target.value
                                    })}
                                    onKeyDown={handleKeydown}
                                />

                                {errors.pincode &&
                                    (errors.pincode.type === 'required' &&
                                        <span className='wcip-text-red-600 wcip-ml-3'>
                                            This field is required*.
                                        </span>
                                    )
                                }

                            </div>

                        </div>

                        <button
                            className="wcip-bg-[#2271b1] wcip-text-white wcip-px-2.5 wcip-rounded-[3px] wcip-min-h-[30px] wcip-max-w-max wcip-mt-4"
                            form="credentials"
                            onSubmit={handleSubmit(handleForm)}
                        >
                            Save Changes
                        </button>

                    </form>

                    <div id="articleId" className="wcip-flex wcip-gap-2 wcip-items-center wcip-mt-8">

                        <p className="wcip-font-semibold wcip-text-[#1d2327]"> Article Ids [{articleIdCount} unused ids available] </p>
                        <button
                            className="wcip-bg-white wcip-border wcip-border-[#0a4b78] wcip-px-2.5 wcip-py-1 wcip-text-[#0a4b78] wcip-rounded-[3px]"
                            onClick={() => { setPopup(true) }}
                        >
                            Import/Export
                        </button>

                    </div>
                </div>

                {popup &&
                    <div className="wcip-relative wcip-mt-8 wcip-bg-white wcip-w-[400px] wcip-h-auto wcip-p-4 wcip-z-[999]">

                        <h3 className="wcip-text-lg wcip-font-bold"> Import/Export Article Ids </h3>
                        <button className="wcip-absolute wcip-top-0 wcip-right-2 wcip-p-2" onClick={() => setPopup(false)}>
                            <span className="wcip-rounded-[50%] wcip-px-1 wcip-py-px wcip-text-white wcip-text-center wcip-bg-[#787c82] hover:wcip-bg-[#d63638] wcip-text-[10px] wcip-my-auto"> X </span>
                        </button>

                        <form
                            id="articleIds"
                            key={2}
                            onSubmit={handleSubmit2(handleArticleIds)}
                            className="wcip-grid wcip-mt-8 wcip-gap-5"
                            method="post"
                            encType="multipart/form-data"
                        >
                            <div className="wcip-flex wcip-flex-col wcip-gap-3 wcip-justify-center">
                                <div className="wcip-flex wcip-items-end">
                                    <input
                                        type="radio"
                                        id="import-append"
                                        name="import-export-type"
                                        value="import-append"
                                        {...register2('importRadio', { required: true })}
                                        onChange={() => { setArticleId({ ...articleId, type: 'append' }) }}
                                        onKeyDown={handleKeydown}
                                    />
                                    <label htmlFor="import-attend"> Append Article Ids </label>
                                </div>

                                <div className="wcip-flex wcip-items-end">
                                    <input
                                        type="radio"
                                        id="import-overwrite"
                                        name="import-export-type"
                                        value="import-overwrite"
                                        {...register2('importRadio', { required: true })}
                                        onChange={() => { setArticleId({ ...articleId, type: 'overwrite' }) }}
                                        onKeyDown={handleKeydown}
                                    />
                                    <label htmlFor="import-ovewrite"> Import and Overwrite Article Ids </label>
                                </div>

                                {/* <div className="wcip-flex wcip-items-end">
                                    <input
                                        type="radio"
                                        id="export"
                                        name="import-export-type"
                                        value="export"
                                        {...register2('importRadio', { required: true })}
                                        onChange={() => { setArticleId({ ...articleId, type: 'export' }) }}
                                        onKeyDown={handleKeydown}
                                    />
                                    <label htmlFor="export"> Export Article Ids </label>
                                </div> */}

                                {errors2.importRadio &&
                                    (errors2.importRadio.type === 'required' &&
                                        <span className='wcip-text-red-600'>
                                            This field is required*.
                                        </span>
                                    )
                                }
                            </div>

                            <input
                                type="file"
                                accept=".csv"
                                {...register2('articleId', { required: true })}
                                onChange={(event) => setArticleId({ ...articleId, file: event.target.files[0] })}
                                onKeyDown={handleKeydown}
                            />
                            {errors2.articleId &&
                                (errors2.articleId.type === 'required' &&
                                    <span className='wcip-text-red-600'>
                                        This field is required*.
                                    </span>
                                )
                            }

                            <button
                                className="wcip-bg-[#2271b1] wcip-text-white wcip-px-2.5 wcip-rounded-[3px] wcip-min-h-[30px] wcip-max-w-max wcip-mt-4"
                                form="articleIds"
                                onSubmit={handleSubmit2(handleArticleIds)}
                            >
                                Import Article Ids
                            </button>
                        </form>
                    </div>
                }

            </>

        );
    }
}

export default Config;