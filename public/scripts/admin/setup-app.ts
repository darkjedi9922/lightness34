import React from 'react';
import ReactDOM from 'react-dom';

export default function setupReactComponent(
    selector: string, 
    component: any
) {
    const element = document.querySelectorAll(selector);
    element.forEach((element) => {
        let props = element.getAttribute('data-props');
        props = (props ? JSON.parse(props) : {});
        ReactDOM.render(React.createElement(component, props), element);
    })
}