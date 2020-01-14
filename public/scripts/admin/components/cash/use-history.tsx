import React from 'react';
import Table from '../table';
import { TableItem, ItemDetails } from '../table/item';
import { isNil } from 'lodash';
import Status, { Type } from '../status';

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

interface CashUseHistoryProps {
    routes: CashRoute[]
}

class CashUseHistory extends React.Component<CashUseHistoryProps> {
    public render(): React.ReactNode {
        return (
            <div className="box box--table">
                <Table
                    headers={[
                        'Route',
                        'Used cash values',
                        'Cash calls',
                        'Status',
                        'Time'
                    ]}
                    items={this.props.routes.map((route) => ({
                        cells: [
                            route.route,
                            route.values.length,
                            route.values.reduce<number>((sum, current) => {
                                return sum + current.calls;
                            }, 0),
                            <div className="stat-status">
                                {route.values.length
                                    ? (
                                        route.values.find((value) => {
                                            return !isNil(value.initError)
                                        })
                                        ? (
                                            <span className="mark2 mark2--red">
                                                Has errors
                                            </span>
                                        ) : (
                                            <span className="mark2 mark2--green">
                                                All OK
                                            </span>
                                        )
                                    ) : (
                                        <span className="mark2 mark2--grey">
                                            No cash
                                        </span>
                                    )
                                }
                            </div>,
                            route.time
                        ],
                        details: (() => {
                            if (!route.values.length) return [];
                            return [{
                                content: (
                                    <Table
                                        className="routes"
                                        headers={[
                                            'Cash',
                                            'Key',
                                            'Initialize time',
                                            'Calls'
                                        ]}
                                        items={route.values.map((cash) => ({
                                            cells: [
                                                cash.class,
                                                cash.key,
                                                <span className="routes__duration">
                                                    {cash.initDurationSec} sec
                                                </span>,
                                                cash.calls
                                            ],
                                            details: (() => {
                                                if (isNil(cash.initError)) return [];
                                                return [{
                                                    content: (
                                                        <Status
                                                            type={Type.ERROR}
                                                            name="Creation error: "
                                                            message={cash.initError}
                                                        />
                                                    )
                                                } as ItemDetails]
                                            })()
                                        }))}
                                    />
                                )
                            } as ItemDetails]
                        })()
                    }) as TableItem)}
                />
            </div>
        )
    }
}

export default CashUseHistory;