import React from "react";

const Invalid = () => {

    const styles = {
        marginTop: "50px",
        padding: "0.5rem",
        background: "#FFFFFF",
        border: "1px solid #C3C4C7",
        borderLeftWidth: "4px",
        borderLeftColor: "#D63638",
        color: "#3C434A",
        fontWeight: "600",
    };

    return (
        <>
            <div style={styles}>
                <p> Your License Key is Invalid Please Contact Vendor </p>
            </div>
        </>
    )
}

export default Invalid;