import React from 'react';
import Table from '../table';
import { TableItem, ItemDetails } from '../table/item';

interface CashValue {
    class: string,
    key: string,
    initDurationSec: number,
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
                    headers={['Route', 'Used cash values', 'Cash calls', 'Time']}
                    items={this.props.routes.map((route) => ({
                        cells: [
                            route.route,
                            route.values.length,
                            route.values.reduce<number>((sum, current) => {
                                return sum + current.calls;
                            }, 0),
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
                                            ]
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