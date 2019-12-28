import React from 'react';

interface Subscriber {

}

interface Emit {

}

interface Handle {
    emitId: number
    subscriberId: number,
}

interface Route {
    route: string,
    subscribers: { [id: number]: Subscriber },
    emits: { [id: number]: Emit },
    handles: Handle[]
}

interface EventsProps {
    routes: { [id: number]: Route }
}

class Events extends React.Component<EventsProps> {
    public constructor(props: EventsProps) {
        super(props);
    }

    public render(): React.ReactNode {
        return (<>
            <div className="box box--table">
                <table className="table routes">
                    <thead>
                        <tr className="table__headers">
                            <td className="table__header">Path</td>
                            <td className="table__header">Subscribers</td>
                            <td className="table__header">Emits</td>
                            <td className="table__header">Handles</td>
                        </tr>
                    </thead>
                    {Object.keys(this.props.routes).map((id) => {
                        let route = this.props.routes[id] as Route;
                        return (
                            <tbody key={id} className="table__item-wrapper">
                                <tr className="table__item">
                                    <td className="table__cell">
                                        <span className={"routes__pagename" 
                                            + (route.route === '' ?
                                                ' routes__pagename--index' : '')
                                        }>
                                            {route.route !== '' ? 
                                                route.route : 'index request'
                                            }
                                        </span>
                                    </td>
                                    <td className="table__cell">
                                        {Object.keys(route.subscribers).length}
                                    </td>
                                    <td className="table__cell">
                                        {Object.keys(route.emits).length}
                                    </td>
                                    <td className="table__cell">
                                        {route.handles.length}
                                    </td>
                                </tr>
                            </tbody>
                        )
                    })}
                </table>
            </div>
        </>);
    }
}

export default Events;