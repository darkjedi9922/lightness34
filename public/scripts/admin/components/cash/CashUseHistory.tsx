import React from 'react';
import Table, { SortOrder } from '../table/Table';
import { isNil } from 'lodash';
import Status, { Type } from '../common/Status';
import RouteRequest from '../routes/RouteRequest';
import HistoryPage from '../stats/HistoryPage';

interface CashValue {
    class: string,
    key: string,
    initDurationSec: number,
    initError?: string,
    calls: number
}

interface CashRoute {
    route: string,
    values: CashValue[],
    time: string
}

enum CashRouteStatus { NO_CASH = 0, ALL_OK = 1, HAS_ERRORS = 2 }

class CashUseHistory extends React.Component {
    // MAP к значениям CashRouteStatus
    private statusMarks = [
        <span className="mark2 mark2--grey">No cash</span>,
        <span className="mark2 mark2--green">All OK</span>,
        <span className="mark2 mark2--red">Has errors</span>
    ]
    public render(): React.ReactNode {
        return <HistoryPage
            breadcrumbsNamePart="Кэш"
            apiDataUrl="/api/stats/cash/history"
            tableBuilder={{
                headers: ['Route', 'Used cash values', 'Cash calls', 'Status', 'Time'],
                defaultSortColumnIndex: 4,
                defaultSortOrder: SortOrder.DESC,
                mapHeadersToSortFields: [
                    'route_url',
                    'value_count',
                    'call_count',
                    'status',
                    'route_id'
                ],
                buildRowCells: (route: CashRoute) => [
                    <RouteRequest route={route.route} />,
                    route.values.length,
                    this.getCashCalls(route),
                    <div className="stat-status">{this.statusMarks[this.getStatus(route)]}</div>,
                    route.time
                ],
                buildRowDetails: (route: CashRoute) => (() => {
                    if (!route.values.length) return [];
                    return [{
                        content: (
                            <Table
                                className="routes"
                                headers={['Cash', 'Key', 'Initialize time', 'Calls']}
                                items={route.values.map((cash) => ({
                                    cells: [
                                        cash.class,
                                        cash.key,
                                        <span className="routes__duration">{cash.initDurationSec} sec</span>,
                                        cash.calls
                                    ],
                                    details: (() => {
                                        if (isNil(cash.initError)) return [];
                                        return [{
                                            content: <Status
                                                type={Type.ERROR}
                                                name="Creation error: "
                                                message={cash.initError}
                                            />
                                        }]
                                    })()
                                }))}
                            />
                        )
                    }]
                })()
            }}
        />
    }

    private getCashCalls(route: CashRoute): number {
        return route.values.reduce<number>((sum, current) => sum + current.calls, 0)
    }

    private getStatus(route: CashRoute): CashRouteStatus {
        return route.values.length
            ? (
                route.values.find((value) => !isNil(value.initError))
                    ? CashRouteStatus.HAS_ERRORS
                    : CashRouteStatus.ALL_OK
            ) : CashRouteStatus.NO_CASH;
    }
}

export default CashUseHistory;