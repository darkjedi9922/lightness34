import React from 'react';
import classNames from 'classnames';
import { isNil } from 'lodash';

interface Props {
    route: string,
    label?: string
}

const RouteRequest = function(props: Props) {
    return <span className={classNames([
        'routes__pagename',
        { 'routes__pagename--index': props.route === '' }
    ])}>
        {props.route !== ''
            ? props.route
            : (isNil(props.label) ? 'index request' : props.label)}
    </span>
};

export default RouteRequest;