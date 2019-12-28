import React from 'react';
import BoxTable, { TableItem, ItemDetails } from './box-table';
import SubscriberList, { Subscriber } from './events/subscriber-list';
import RouteRequest from './routes/Request';

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
        const items: TableItem[] = [];
        for (const id in this.props.routes) {
            if (this.props.routes.hasOwnProperty(id)) {
                const route = this.props.routes[id];
                const cells = [
                    <RouteRequest route={route.route} />,
                    Object.keys(route.subscribers).length,
                    Object.keys(route.emits).length,
                    route.handles.length
                ];
                
                const subscribers = [];
                for (const id in route.subscribers) {
                    if (route.subscribers.hasOwnProperty(id)) {
                        const subscriber = route.subscribers[id];
                        subscribers.push({
                            ...subscriber,
                            handles: this.findSubscriberHandleEmitsId(
                                route,
                                parseInt(id)
                            )
                        });
                    }
                }

                const details: ItemDetails[] = [
                    {
                        title: 'Subscribers',
                        content: <SubscriberList subscribers={subscribers} />
                    }
                ];
                items.push({ cells, details })
            }
        }

        return (
            <BoxTable
                className="routes"
                headers={['Path', 'Subscribers', 'Emits', 'Handles']}
                items={items}
            ></BoxTable>
        );
    }

    private findSubscriberHandleEmitsId(
        route: Route,
        subscriberId: number
    ): number[] {
        const result = [];
        for (let i = 0; i < route.handles.length; i++) {
            const handle = route.handles[i];
            if (handle.subscriberId === subscriberId) {
                result.push(handle.emitId);
            }
        }
        return result;
    }
}

export default Events;