import React from 'react';
import ContentHeader from '../../content-header';
import Breadcrumbs from '../../common/Breadcrumbs';
import Table, { SortOrder } from '../../table/table';
import RouteRequest from '../../routes/request';
import Parameter from '../../parameter';
import Status, { Type } from '../../status';
import LoadingContent from '../../loading-content';
import $ from 'jquery';

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

interface RouteHistoryAPIResult {
    clearActionUrl?: string,
    routes: Route[]
}

interface RouteHistoryState extends RouteHistoryAPIResult {}

class RouteHistory extends React.Component<{}, RouteHistoryState> {
    public constructor(props) {
        super(props);
        this.state = {
            // Поле используется также как флаг окончания получения ответа с сервера.
            clearActionUrl: null,
            routes: []
        }
    }

    public componentDidMount(): void {
        $.ajax({
            url: '/api/stats/routes/history',
            dataType: 'json',
            success: (result: RouteHistoryState) => {
                this.setState(result)
            }
        })
    }

    public render(): React.ReactNode {
        const state = this.state;
        return <>
            <ContentHeader>
                <Breadcrumbs items={[
                    { name: 'Мониторинг' },
                    { name: 'Маршруты' },
                    { name: `История ${state.clearActionUrl 
                        ? '(' + state.routes.length + ')' 
                        : ''}`
                    }
                ]} />
                {state.clearActionUrl && <a
                    href={state.clearActionUrl}
                    className="button"
                >Очистить статистику</a>}
            </ContentHeader>
            <LoadingContent>
                {state.clearActionUrl && <div className="box box--table">
                    <Table
                        className="routes"
                        collapsable={true}
                        headers={['Path', 'Load', 'Code', 'Time']}
                        sort={{
                            defaultCellIndex: 3,
                            defaultOrder: SortOrder.DESC,
                            isAlreadySorted: true
                        }}
                        items={state.routes.map((route) => ({
                            pureCellsToSort: [
                                route.route,
                                route.loadSeconds,
                                route.code,
                                route.time
                            ],
                            cells: [
                                <>
                                    <RouteRequest route={route.route} />
                                    {route.ajax && <span
                                        className="routes__mark routes__mark--ajax"
                                    >ajax</span>}
                                    &nbsp;
                                    {route.type === 'action' && <span 
                                        className="routes__mark routes__mark--action"
                                    >action</span>}
                                    {route.type === 'dynamic' && <span
                                        className="routes__mark routes__mark--dynamic"
                                    >dynamic</span>}
                                </>,
                                <span className="table__duration">
                                    {route.loadSeconds} sec
                                </span>,
                                <span className={`routes__code
                                    routes__code--${this.getStatusType(route.code)}`
                                }>{route.code}</span>,
                                <span className="routes__time">{route.time}</span>
                            ],
                            details: [
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
                                            <Parameter
                                                key={i}
                                                name={i.toString()}
                                                value={value}
                                            />
                                        )
                                    }]
                                })(),
                                ...(() => {
                                    if (!Object.keys(route.args).length) return [];
                                    return [{
                                        title: 'Get',
                                        content: Object.keys(route.args).map((key, i) => 
                                            <Parameter
                                                key={i}
                                                name={key}
                                                value={route.args[key]}
                                            />
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
                        }))}
                    />
                </div>}
            </LoadingContent>
        </>
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