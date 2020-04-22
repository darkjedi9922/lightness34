import React from 'react';
import { SortOrder } from '../../table/table';
import RouteRequest from '../../routes/request';
import Parameter from '../../parameter';
import Status, { Type } from '../../status';
import History from '../../stats/history';

interface Route {
    route: string,
    ajax: boolean,
    type: 'page' | 'action' | 'dynamic',
    loadSeconds: number,
    time: string,
    viewfile?: string,
    args: {[key: string]: string},
    dynamicParams?: string[],
    code: number,
    codeInfo?: string
}

class RouteHistory extends React.Component {
    public render(): React.ReactNode {
        return <History
            breadcrumbsNamePart="Маршруты"
            apiDataUrl="/api/stats/routes/history"
            tableBuilder={{
                className: 'routes',
                headers: ['Path', 'Load', 'Code', 'Time'],
                defaultSortColumnIndex: 3,
                defaultSortOrder: SortOrder.DESC,
                buildPureValuesToSort: (route: Route) => [
                    route.route,
                    route.loadSeconds,
                    route.code,
                    route.time
                ],
                buildRowCells: (route: Route) => [
                    <>
                        <RouteRequest route={route.route} />
                        {route.ajax && <span className="routes__mark routes__mark--ajax">ajax</span>}
                        &nbsp;
                        {route.type === 'action' && <span className="routes__mark routes__mark--action">action</span>}
                        {route.type === 'dynamic' && <span className="routes__mark routes__mark--dynamic">dynamic</span>}
                    </>,
                    <span className="table__duration">{route.loadSeconds} sec</span>,
                    <span className={`routes__code routes__code--${this.getStatusType(route.code)}`}>{route.code}</span>,
                    <span className="routes__time">{route.time}</span>
                ],
                buildRowDetails: (route: Route) => [
                    ...(() => {
                        if (route.viewfile === null) return [];
                        return [{
                            title: 'View file',
                            content: <Parameter value={route.viewfile} />
                        }]
                    })(),
                    ...(() => {
                        if (!route.dynamicParams.length) return [];
                        return [{
                            title: 'Dynamic Page Arguments',
                            content: route.dynamicParams.map((value, i) =>
                                <Parameter key={i} name={i.toString()} value={value} />
                            )
                        }]
                    })(),
                    ...(() => {
                        if (!Object.keys(route.args).length) return [];
                        return [{
                            title: 'Get',
                            content: Object.keys(route.args).map((key, i) =>
                                <Parameter key={i} name={key} value={route.args[key]} />
                            )
                        }]
                    })(),
                    ...(() => {
                        if (!route.codeInfo) return [];
                        return [{
                            content: <Status
                                type={this.getStatusType(route.code)}
                                name={`Status ${route.code} `}
                                message={route.codeInfo}
                            />
                        }]
                    })()
                ]
            }}
        />
    }

    private getStatusType(code: number): Type {
        switch ((code / 100) | 0) {
            case 4: return Type.WARNING;
            case 5: return Type.ERROR;
            default: return Type.OK;
        }
    }
}

export default RouteHistory;