import React from 'react';
import Table from '../table';
import { TableItem } from '../table/item';

interface CashRoute {
    route: string,
    usedCashValues: number,
    cashCalls: number,
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
                            route.usedCashValues,
                            route.cashCalls,
                            route.time
                        ]
                    }) as TableItem)}
                ></Table>
            </div>
        )
    }
}

export default CashUseHistory;