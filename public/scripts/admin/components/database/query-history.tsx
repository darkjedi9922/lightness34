import React from 'react'
import Table from '../table';
import { TableItem, ItemDetails } from '../table/item';
import Parameter from '../parameter';
import RouteRequest from '../routes/Request';
import Status, { Type } from '../status';
import { isNil } from 'lodash';

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
                                <div className="query__sql">
                                    <Parameter
                                        number={i + 1}
                                        value={query.sql}
                                    ></Parameter>
                                    <span className="query__duration">
                                        {`${query.durationSec} sec`}
                                    </span>
                                </div>
                                {!isNil(query.error) &&
                                    <Status 
                                        type={Type.ERROR}
                                        name="Error "
                                        message={query.error}
                                    ></Status>
                                }
                            </div>
                        )}
                    </>
                );
                details.push({
                    title: 'Queries',
                    content: queryList
                })
            }
            items.push({
                cells: [
                    <RouteRequest route={route.route}></RouteRequest>,
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