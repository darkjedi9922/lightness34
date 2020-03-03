import React from 'react';
import classNames from 'classnames';
import Item, { TableItem } from './item';

export enum SortOrder {
    ASC = 0,
    DESC = 1
}

interface SortParameters {
    defaultCellIndex: number,
    defaultOrder: SortOrder,
    isAlreadySorted: boolean,
    onSort?: (column: number, order: SortOrder) => void
}

interface SortedTableItem extends TableItem {
    // Указывать, если нужно сортировать таблицу по значениям.
    pureCellsToSort?: (number|string)[]
}

interface TableProps {
    className?: string
    headers?: string[],
    items: SortedTableItem[],
    collapsable?: boolean,
    sort?: SortParameters
}

interface SortState {
    sortedItems: SortedTableItem[],
    column: number,
    order: SortOrder
}

interface TableState {
    sort?: SortState
}

class Table extends React.Component<TableProps, TableState> {
    public constructor(props: TableProps) {
        super(props);
        if (props.sort) {
            this.state = {
                sort: {
                    sortedItems: [...props.items],
                    column: props.sort.defaultCellIndex,
                    order: props.sort.defaultOrder
                }
            }
            if (!props.sort.isAlreadySorted) this.sort(
                props.sort.defaultCellIndex,
                props.sort.defaultOrder
            );
        } else this.state = { sort: null }
    }

    public render(): React.ReactNode {
        const state = this.state;
        const tableClasses = classNames('table', this.props.className);
        return (
            <table className={tableClasses}>
                {this.props.headers && this.props.headers.length &&
                    <thead>
                        <tr className="table__headers">
                            {this.props.collapsable &&
                                <td className="table__header"></td>
                            }
                            {this.props.headers.map((name, index) => 
                                <td
                                    key={index}
                                    className="table__header"
                                >
                                    <span 
                                        className={classNames(
                                            "table__title",
                                            {'table__title--sortable': state.sort},
                                            {'table__title--sorted': state.sort &&
                                                state.sort.column === index
                                            }
                                        )} 
                                        onClick={state.sort
                                            ? () => this.toggleSort(index)
                                            : null
                                        }
                                    >
                                        {name}
                                        {state.sort && state.sort.column === index &&
                                            <i className={classNames({
                                                'icon-up-dir': state.sort.order 
                                                    === SortOrder.ASC,
                                                'icon-down-dir': state.sort.order
                                                    === SortOrder.DESC
                                            })}></i>
                                        }
                                    </span>
                                </td>
                            )}
                        </tr>
                    </thead>
                }
                {this.state.sort
                    ? this.state.sort.sortedItems.map((item) => <Item 
                        key={this.props.items.indexOf(item)}
                        item={item}
                        collapsable={this.props.collapsable}
                    />)
                    : this.props.items.map((item, index) => <Item
                        key={index}
                        item={item}
                        collapsable={this.props.collapsable}
                    />)
                }
            </table>
        );
    }

    private sort(column: number, order: SortOrder) {
        if (!this.state.sort) return;
        this.setState((state) => ({
            sort: {
                sortedItems: state.sort.sortedItems.sort((a, b) => {
                    const aValue = a.pureCellsToSort[column];
                    const bValue = b.pureCellsToSort[column];
                    const orderK = order === SortOrder.ASC ? 1 : -1;
                    if (aValue > bValue) return 1 * orderK;
                    else if (aValue < bValue) return -1 * orderK;
                    return 0;
                }),
                column: column,
                order: order
            }
        }));
        this.props.sort && this.props.sort.onSort(column, order);
    }

    private toggleSort(column: number) {
        if (this.state.sort.column === column) {
            this.sort(column, this.state.sort.order === SortOrder.ASC 
                ? SortOrder.DESC
                : SortOrder.ASC
            );
        } else this.sort(column, this.state.sort.order);
    }
}

export default Table;