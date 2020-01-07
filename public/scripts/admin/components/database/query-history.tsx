import React from 'react'
import Table from '../table';
import { TableItem, ItemDetails } from '../table/item';
import Parameter from '../parameter';

interface Query {
    sql: string,
    durationSec: number
}

interface QueryRoute {
    route: string
    queries: Query[],
    time: string
}

interface QueryHistoryProps {
    routes: QueryRoute[]
}

class QueryHistory extends React.Component<QueryHistoryProps> {
    public render(): React.ReactNode {
        const items: TableItem[] = [];
        this.props.routes.map((route, i) => {
            const details: ItemDetails[] = [];
            if (route.queries.length) {
                const queryList = (
                    <>
                        {route.queries.map((query, i) =>
                            <div key={i} className="query">
                                <Parameter
                                    number={i + 1}
                                    value={query.sql}
                                ></Parameter>
                                <span className="query__duration">
                                    {`${query.durationSec} sec`}
                                </span>
                            </div>
                        )}
                    </>
                );
                details.push({ content: queryList })
            }
            items.push({
                cells: [
                    route.route,
                    route.queries.length,
                    route.time
                ],
                details
            })
        });
        return (
            <div className="box box--table">
                <Table
                    className="routes"
                    headers={['Route', 'Queries', 'Time']}
                    items={items}
                ></Table>
            </div>
        )
    }
}

export default QueryHistory;