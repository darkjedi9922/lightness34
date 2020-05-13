import React from 'react';
import classNames from 'classnames';
import TableItem, { TableItemData } from './TableItem';
import { isNil } from 'lodash';

export enum SortOrder {
    ASC = 'asc',
    DESC = 'desc'
}

interface SortParameters {
    sortableCells?: number[],
    defaultCellIndex: number,
    defaultOrder: SortOrder,
    isAlreadySorted: boolean,
    onSort?: (column: number, order: SortOrder) => void
}

interface SortedTableItem extends TableItemData {
    // Указывать, если нужно сортировать таблицу по значениям.
    pureCellsToSort?: (number|string)[]
}

interface TableProps {
    className?: string
    headers?: any[],
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
        this.state = { sort: this.getSortStateFromProps(props) };
    }

    public UNSAFE_componentWillReceiveProps(
        nextProps: TableProps,
        nextState: TableState
    ) {
        if (this.props.items[0] === nextProps.items[0]) return;
        if (!this.props.sort) return;
        this.setState({ sort: this.getSortStateFromProps(nextProps) })
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
                                            {'table__title--sortable':
                                                this.isColumnSortable(index)
                                            },
                                            {'table__title--sorted': state.sort &&
                                                state.sort.column === index
                                            }
                                        )} 
                                        onClick={this.isColumnSortable(index)
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
                    ? this.state.sort.sortedItems.map((item) => <TableItem 
                        key={this.props.items.indexOf(item)}
                        item={item}
                        collapsable={this.props.collapsable}
                    />)
                    : this.props.items.map((item, index) => <TableItem
                        key={index}
                        item={item}
                        collapsable={this.props.collapsable}
                    />)
                }
            </table>
        );
    }

    private getSortStateFromProps(props: TableProps) {
        return props.sort 
            ? {
                sortedItems: props.sort.isAlreadySorted
                    ? [...props.items]
                    : this.sortItems(
                        props.items,
                        props.sort.defaultCellIndex,
                        props.sort.defaultOrder
                    ),
                column: props.sort.defaultCellIndex,
                order: props.sort.defaultOrder
            }
            : null;
    }

    private isColumnSortable(index: number): boolean {
        return this.props.sort 
            && (isNil(this.props.sort.sortableCells)
                || this.props.sort.sortableCells.indexOf(index) !== -1
            )
    }

    private sort(column: number, order: SortOrder) {
        if (!this.state.sort) return;
        if (this.props.sort.onSort) {
            this.props.sort.onSort(column, order);
            return;
        }
        this.setState((state) => ({
            sort: {
                sortedItems: this.sortItems(state.sort.sortedItems, column, order),
                column: column,
                order: order
            }
        }));
    }

    private sortItems(
        items: SortedTableItem[],
        column: number,
        order: SortOrder
    ): SortedTableItem[] {
        return items.sort((a, b) => {
            const aValue = a.pureCellsToSort[column];
            const bValue = b.pureCellsToSort[column];
            const orderK = (order === SortOrder.ASC ? 1 : -1);
            if (aValue > bValue) return 1 * orderK;
            else if (aValue < bValue) return -1 * orderK;
            return 0;
        })
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