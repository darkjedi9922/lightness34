import React from 'react';
import { SortOrder } from '../table/table';
import SubscriberList from './subscriber-list';
import RouteRequest from '../routes/request';
import EmitList from './emit-list';
import { Subscriber, Emit, Handle } from '../../structures';
import History from '../stats/history';

interface Route {
    route: string,
    subscribers: Subscriber[],
    emits: Emit[],
    handles: Handle[],
    time: string,
}

class EventHistory extends React.Component {
    public render(): React.ReactNode {
        return <History
            breadcrumbsNamePart="События"
            apiDataUrl="/api/stats/events/history"
            tableBuilder={{
                className: 'routes',
                headers: ['Path', 'Subscribers', 'Emits', 'Handles', 'Time'],
                defaultSortColumnIndex: 4,
                defaultSortOrder: SortOrder.DESC,
                buildPureValuesToSort: (route: Route) => [
                    route.route,
                    route.subscribers.length,
                    route.emits.length,
                    route.handles.length,
                    route.time
                ],
                buildRowCells: (route: Route) => [
                    <RouteRequest route={route.route} />,
                    route.subscribers.length,
                    route.emits.length,
                    route.handles.length,
                    route.time
                ],
                buildRowDetails: (route: Route) => [{
                    title: 'Subscribers',
                    content: <SubscriberList
                        subscribers={route.subscribers}
                        handles={route.handles}
                    ></SubscriberList>
                }, {
                    content: <EmitList
                        emits={route.emits}
                        handles={route.handles}
                        subscribers={route.subscribers}
                    ></EmitList>
                }]
            }}
        />
    }
}

export default EventHistory;