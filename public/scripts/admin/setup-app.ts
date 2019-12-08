import React from 'react';
import ReactDOM from 'react-dom';

export default function setupReactComponent(
    elementId: string, 
    component: any, 
    data?: any
) {
    const element = document.getElementById(elementId);
    if (element) {
        ReactDOM.render(React.createElement(component, data), element);
    }
}