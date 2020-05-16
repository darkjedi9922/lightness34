import React from 'react';
import Table, { SortOrder } from '../table/Table';
import RouteRequest from '../routes/RouteRequest';
import { isNil } from 'lodash';
import Status, { Type } from '../common/Status';
import Parameter from '../common/Parameter';
import { DetailsProps } from '../table/TableDetails';
import HistoryPage from '../stats/HistoryPage';

interface MetaData {
    name: string,
    value: string,
    type: string
}

interface ViewStat {
    id: number,
    class: string,
    name: string,
    file: string,
    layoutName?: string,
    parentId?: number,
    error?: string,
    durationSec: number,
    meta: MetaData[]
}

interface ViewRoute {
    route: string
    views: ViewStat[],
    sumLoad: number,
    status: ViewRouteStatus,
    time: string
}

interface ViewsHistoryProps {
    routes: ViewRoute[],
}

enum ViewRouteStatus { NO_VIEWS = 0, ALL_OK = 1, HAS_ERRORS = 2 }

class ViewHistory extends React.Component<ViewsHistoryProps> {
    // MAP к значениям ViewRouteStatus
    private statusMarks = [
        <span className="mark2 mark2--grey">No views</span>,
        <span className="mark2 mark2--green">All OK</span>,
        <span className="mark2 mark2--red">Has errors</span>
    ]

    public render(): React.ReactNode {
        return <HistoryPage
            breadcrumbsNamePart="Представления"
            apiDataUrl="/api/stats/views/history"
            tableBuilder={{
                className: "routes",
                headers: ['Route', 'Views', 'Sum load', 'Status', 'Time'],
                defaultSortColumnIndex: 4,
                defaultSortOrder: SortOrder.DESC,
                mapHeadersToSortFields: [
                    'route_url',
                    'view_count',
                    'sum_load',
                    'status',
                    'route_id'
                ],
                buildRowCells: (route: ViewRoute) => [
                    <RouteRequest route={route.route} />,
                    route.views.length,
                    <span className="table__duration">{route.sumLoad} sec</span>,
                    <div className="stat-status">{this.statusMarks[route.status]}</div>,
                    route.time
                ],
                buildRowDetails: (route: ViewRoute) => (() => {
                    if (!route.views.length) return [{
                        content: <Status type={Type.EMPTY} message="No views"/>
                    }];
                    return [{
                        content: (
                            <Table
                                collapsable={true}
                                headers={['Class', 'Name', 'Layout', 'Load']}
                                items={route.views.map((view) => ({
                                    cells: [
                                        view.class,
                                        <>
                                            <RouteRequest route={view.name} label="index view" />
                                            {!isNil(view.error) && <>&nbsp;<span className="mark mark--red">Error</span></>}
                                            {view.meta.length > 0 && <>&nbsp;<span className="mark mark--blue">{view.meta.length} meta</span></>}
                                        </>,
                                        !isNil(view.layoutName)
                                            ? (
                                                view.layoutName !== ''
                                                    ? view.layoutName
                                                    : <span className="routes__pagename routes__pagename--index">index layout</span>
                                            )
                                            : <Status type={Type.NONE} message="None" />,
                                        <span className="routes__duration">{view.durationSec} sec</span>
                                    ],
                                    details: (() => {
                                        const details: DetailsProps[] = [{
                                            title: 'File',
                                            content: view.file
                                        }, {
                                            title: 'Parent',
                                            content: !isNil(view.parentId)
                                                ? route.views.find((stat) => stat.id === view.parentId).file
                                                : <Status type={Type.EMPTY} message="No parent" />
                                        }, {
                                            title: 'Meta Data',
                                            content: view.meta.length
                                                ? view.meta.map((data, i) => <Parameter 
                                                    key={i} 
                                                    name={data.name} 
                                                    value={data.value} 
                                                    type={data.type}
                                                />)
                                                : <Status type={Type.EMPTY} message="No data" />
                                        }];
                                        if (!isNil(view.error)) {
                                            details.push({
                                                content: <Status type={Type.ERROR} name="Error: " message={view.error} />
                                            })
                                        }
                                        return details;
                                    })()
                                }))}
                            />
                        )
                    }]
                })()
            }}
        />
    }
}

export default ViewHistory;