import React from 'react';

interface Props {
    route: string
}

class RouteRequest extends React.Component<Props> {
    public render(): React.ReactNode {
        return (
            <span className={"routes__pagename"
                + (this.props.route === '' ? ' routes__pagename--index' : '')
            }>
                {this.props.route !== '' ? this.props.route : 'index request'}
            </span>
        );
    }
}

export default RouteRequest;