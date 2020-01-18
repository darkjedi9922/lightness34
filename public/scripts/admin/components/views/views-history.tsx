import React from 'react';
import Breadcrumbs from '../common/Breadcrumbs';
import Table from '../table';
import RouteRequest from '../routes/request';
import { isNil } from 'lodash';
import Status, { Type } from '../status';
import { ItemDetails } from '../table/item';
import Parameter from '../parameter';

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
    views: ViewStat[]
}

interface ViewsHistoryProps {
    routes: ViewRoute[],
    clearStatsUrl: string
}

class ViewsHistory extends React.Component<ViewsHistoryProps> {
    public render(): React.ReactNode {
        return <>
            <div className="content__header">
                <Breadcrumbs items={[{
                    name: 'Мониторинг'
                }, {
                    name: 'Виды'
                }]} />
                <a href={this.props.clearStatsUrl} className="button">
                    Очистить статистику
                </a>
            </div>
            <span className="content__title">
                История ({this.props.routes.length})
            </span>
            <div className="box box--table">
                <Table 
                    className="routes"
                    collapsable={true}
                    headers={['Route', 'Views', 'Status']}
                    items={this.props.routes.map((route) => ({
                        cells: [
                            <RouteRequest route={route.route} />,
                            route.views.length,
                            <div className="stat-status">
                                {route.views.length
                                    ? (
                                        route.views.find((view) =>
                                            !isNil(view.error))
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
                                            No views
                                        </span>
                                    )
                                }
                            </div>
                        ],
                        details: (() => {
                            if (!route.views.length) return [{
                                content: (
                                    <Status 
                                        type={Type.EMPTY}
                                        message="No views"
                                    />
                                )
                            }];
                            return [{
                                content: (
                                    <Table
                                        collapsable={true}
                                        headers={[
                                            'Class',
                                            'Name',
                                            'Layout',
                                            'Load'
                                        ]}
                                        items={route.views.map((view) => ({
                                            cells: [
                                                view.class,
                                                <>
                                                    {view.name}
                                                    {!isNil(view.error) &&
                                                        <>
                                                            &nbsp;
                                                            <span className="mark mark--red">
                                                                Error
                                                            </span>
                                                        </>
                                                    }
                                                    {view.meta.length > 0 &&
                                                        <>
                                                            &nbsp;
                                                            <span className="mark mark--blue">
                                                                Meta
                                                            </span>
                                                        </>
                                                    }
                                                </>,
                                                !isNil(view.layoutName)
                                                ? (
                                                    view.layoutName !== ''
                                                    ? view.layoutName
                                                    : (
                                                        <span className="routes__pagename routes__pagename--index">
                                                            index layout
                                                        </span>   
                                                    )
                                                )
                                                : (
                                                    <Status
                                                        type={Type.NONE}
                                                        message="None"
                                                    />
                                                ),
                                                <span 
                                                    className="routes__duration"
                                                >
                                                    {view.durationSec} sec
                                                </span>
                                            ],
                                            details: (() => {
                                                const details: ItemDetails[] = [{
                                                    title: 'File',
                                                    content: view.file
                                                }, {
                                                    title: 'Parent',
                                                    content: !isNil(view.parentId)
                                                    ? route.views.find((stat) =>
                                                        stat.id === view.parentId
                                                    ).file
                                                    : (
                                                        <Status
                                                            type={Type.EMPTY}
                                                            message="No parent"
                                                        />
                                                    )
                                                }, {
                                                    title: 'Meta Data',
                                                    content: view.meta.length
                                                    ? view.meta.map(
                                                        (data, i) => (
                                                            <Parameter
                                                                key={i}
                                                                name={data.name}
                                                                value={data.value}
                                                                type={data.type}
                                                            />
                                                        )
                                                    )
                                                    : (
                                                        <Status
                                                            type={Type.EMPTY}
                                                            message="No data"
                                                        />
                                                    )
                                                }];
                                                if (!isNil(view.error)) {
                                                    details.push({
                                                        content: (
                                                            <Status
                                                                type={Type.ERROR}
                                                                name="Error: "
                                                                message={view.error}
                                                            />
                                                        )
                                                    })
                                                }
                                                return details;
                                            })()
                                        }))}
                                    />
                                )
                            }]
                        })()
                    }))}
                />
            </div>
        </>
    }
}

export default ViewsHistory;