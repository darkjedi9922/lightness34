import React from 'react';
import classNames from 'classnames';
import { isNil } from 'lodash';

interface Props {
    route: string,
    label?: string
}

class RouteRequest extends React.Component<Props> {
    public render(): React.ReactNode {
        return (
            <span className={classNames([
                'routes__pagename',
                {'routes__pagename--index': this.props.route === ''}
            ])}>
                {this.props.route !== '' 
                    ? this.props.route 
                    : (isNil(this.props.label) ? 'index request' : this.props.label)}
            </span>
        );
    }
}

export default RouteRequest;