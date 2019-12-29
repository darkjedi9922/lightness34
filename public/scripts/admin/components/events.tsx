import React from 'react';
import Table, { TableItem, ItemDetails } from './table';
import SubscriberList from './events/subscriber-list';
import RouteRequest from './routes/Request';
import EmitList from './events/emit-list';
import { Subscriber, Emit, Handle } from '../structures';

interface Route {
    route: string,
    subscribers: Subscriber[],
    emits: Emit[],
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
        const items: TableItem[] = [];
        for (const id in this.props.routes) {
            if (this.props.routes.hasOwnProperty(id)) {
                const route = this.props.routes[id];
                const cells = [
                    <RouteRequest route={route.route} />,
                    route.subscribers.length,
                    route.emits.length,
                    route.handles.length
                ];
                
                const details: ItemDetails[] = [{
                    title: 'Subscribers',
                    content: <SubscriberList 
                        subscribers={route.subscribers}
                        handles={route.handles}
                    ></SubscriberList>
                }, {
                    content: <EmitList 
                        emits={route.emits}
                        handles={route.handles}
                    ></EmitList>
                }];

                items.unshift({ cells, details })
            }
        }

        return (
            <div className="box box--table">
                <Table
                    className="routes"
                    headers={['Path', 'Subscribers', 'Emits', 'Handles']}
                    items={items}
                ></Table>
            </div>
        );
    }
}

export default Events;