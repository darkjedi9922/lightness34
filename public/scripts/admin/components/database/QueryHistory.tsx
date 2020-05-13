import React from 'react'
import { SortOrder } from '../table/Table';
import Parameter from '../common/Parameter';
import RouteRequest from '../routes/RouteRequest';
import Status, { Type } from '../common/Status';
import { isNil } from 'lodash';
import HistoryPage from '../stats/HistoryPage';

interface Query {
    sql: string,
    error?: string,
    durationSec: number
}

interface QueryRoute {
    route: string
    queries: Query[],
    time: string
}

interface Props {
    routes: QueryRoute[]
}

enum QueryRouteStatus { NO_QUERIES = 0, ALL_OK = 1, HAS_ERRORS = 2 }

class QueryHistory extends React.Component<Props> {
    // MAP к значениям QueryRouteStatus
    private statusMarks = [
        <span className="mark2 mark2--grey">No queries</span>,
        <span className="mark2 mark2--green">All OK</span>,
        <span className="mark2 mark2--red">Has errors</span>
    ]
    public render(): React.ReactNode {
        return <HistoryPage
            breadcrumbsNamePart="Запросы"
            apiDataUrl="/api/stats/queries/history"
            tableBuilder={{
                className: 'routes queries',
                headers: ['Route', 'Queries', 'Sum load', 'Status', 'Time'],
                defaultSortColumnIndex: 4,
                defaultSortOrder: SortOrder.DESC,
                buildPureValuesToSort: (route: QueryRoute) => [
                    route.route,
                    route.queries.length,
                    this.calcSumLoad(route),
                    this.getStatus(route),
                    route.time
                ],
                buildRowCells: (route: QueryRoute) => [
                    <RouteRequest route={route.route}></RouteRequest>,
                    route.queries.length,
                    <span className="table__duration">{this.calcSumLoad(route)} sec</span>,
                    <div className="stat-status">{this.statusMarks[this.getStatus(route)]}</div>,
                    route.time
                ],
                buildRowDetails: (route: QueryRoute) => [{
                    title: 'Queries',
                    content: route.queries.map((query, i) =>
                        <div key={i} className="query">
                            <div className="query__sql">
                                <Parameter number={i + 1} value={query.sql} />
                                <span className="query__duration">{query.durationSec} sec</span>
                            </div>
                            {!isNil(query.error) && <Status type={Type.ERROR} name="Error " message={query.error} />}
                        </div>
                    )
                }]
            }}
        />
    }

    private calcSumLoad(route: QueryRoute): number {
        return Math.round(route.queries.reduce<number>(
            (sum, query) => sum + query.durationSec, 0
        ) * 1000000) / 1000000;
    }

    private getStatus(route: QueryRoute): QueryRouteStatus {
        if (route.queries.length === 0) return QueryRouteStatus.NO_QUERIES;
        else if (route.queries.find((query) => !isNil(query.error))) 
            return QueryRouteStatus.HAS_ERRORS;
        else return QueryRouteStatus.ALL_OK;
    }
}

export default QueryHistory;