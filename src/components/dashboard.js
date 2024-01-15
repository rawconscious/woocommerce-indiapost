import React, {
    useState,
    useEffect,
} from 'react';
import Config from './inner-components/config';
import Loading from './inner-components/loading';
import Notice from './inner-components/notice';
import StatusComponent from './inner-components/status-component';

const Dashboard = () => {

    const [pageComponent, setPageComponent] = useState('Ship');
    const [loading, setLoading] = useState(false);
    const [notice, setNotice] = useState({
        status: '',
        message: '',
    });

    const buttonClass = "wcip-border wcip-border-[#c3c4c7] wcip-border-b-0 wcip-bg-[#dcdcde] wcip-px-3 wcip-py-1.5 wcip-text-sm wcip-font-semibold wcip-text-[#50575e] hover:wcip-bg-white";
    const buttonSelectedClass = "wcip-border wcip-border-[#c3c4c7] wcip-border-b-0 wcip--mb-px wcip-bg-[#f0f0f1] wcip-px-3 wcip-py-1.5 wcip-text-sm wcip-font-semibold";

    useEffect(() => {
        setNotice({ status: null, message: null });
        setLoading(true);
        setTimeout(() => {
            setLoading(false);
        }, [1000]);
    }, [pageComponent]);

    return (
        <>
                <>
                    <div className="wcip-border-b wcip-border-[#c3c4c7] wcip-pt-8 wcip-px-2 wcip-flex wcip-flex-wrap wcip-gap-4 wcip-mt-20">

                        <button className={('Ship' === pageComponent) ? buttonSelectedClass : buttonClass} onClick={() => setPageComponent('Ship')}>
                            Ready To Ship
                        </button>

                        <button className={('Dropoff' === pageComponent) ? buttonSelectedClass : buttonClass} onClick={() => setPageComponent('Dropoff')}>
                            Drop-off Booked
                        </button>

                        <button className={('Pickup' === pageComponent) ? buttonSelectedClass : buttonClass} onClick={() => setPageComponent('Pickup')}>
                            Pickup Booked
                        </button>

                        <button className={('DropoffDone' === pageComponent) ? buttonSelectedClass : buttonClass} onClick={() => setPageComponent('DropoffDone')}>
                            Drop-off Completed
                        </button>

                        <button className={('PickupDone' === pageComponent) ? buttonSelectedClass : buttonClass} onClick={() => setPageComponent('PickupDone')}>
                            Pickup Completed
                        </button>

                        {/* <button className={('Errors' === pageComponent) ? buttonSelectedClass : buttonClass} onClick={() => setPageComponent('Errors')}>
                    Errors
                </button> */}

                        <button className={('Config' === pageComponent) ? buttonSelectedClass + ' wcip-ml-auto' : buttonClass + ' wcip-ml-auto'} onClick={() => setPageComponent('Config')}>
                            Config
                        </button>

                    </div>
                    <Notice notice={notice} setNotice={setNotice} />

                    {'Config' === pageComponent ?
                        <Config
                            setNotice={setNotice}
                        />
                        :
                        <StatusComponent
                            setNotice={setNotice}
                            pageComponent={pageComponent}
                        />
                    }

                </>
        </>
    )
}

export default Dashboard;