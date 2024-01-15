import React from 'react';
import ReactDOM from 'react-dom';
import {
    BrowserRouter as Router,
    Routes,
    Route,
} from "react-router-dom";
import Header from './components/header';
import Dashboard from './components/dashboard';

import './rc-wcip.scss';

const WcipMain = () => {
    
    return (
        <>
            <Header />
            <Dashboard />
        </>
    );

};
ReactDOM.render(<WcipMain />, document.getElementById('wcip-react-app'));