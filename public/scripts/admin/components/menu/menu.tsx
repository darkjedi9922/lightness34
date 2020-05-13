import React from 'react';
import MenuItem, { ItemProps } from './MenuItem';

interface MenuProps {
    items: ItemProps[]
}

class Menu extends React.Component<MenuProps> {
    private storage: object = {};

    public constructor(props: MenuProps) {
        super(props);
        this.storage = JSON.parse(sessionStorage['menu'] || '{}');
    }

    public render(): React.ReactNode {
        const items = this.loadItemsCollapseState(this.props.items);
        return <ul className="menu">
            {items.map((item, index) => <MenuItem
                key={index}
                icon={item.icon}
                name={item.name}
                link={item.link}
                submenu={item.submenu}
                _collapsed={item._collapsed}
                _id={`${index}`}
                _onToggle={(id, collapsed) => {
                    if (collapsed) delete this.storage[id];
                    else this.storage[id] = true;
                    sessionStorage['menu'] = JSON.stringify(this.storage);
                }}
            ></MenuItem>)}
        </ul> 
    }

    private loadItemsCollapseState(
        items: ItemProps[],
        parentId: string = null
    ): ItemProps[] {
        const result: ItemProps[] = [];
        for (let i = 0; i < items.length; i++) {
            const item = items[i];
            result.push({ ...item });
            if (item.submenu) {
                const id = (parentId === null ? `${i}` : `${parentId}-${i}`);
                result[i]._collapsed = !this.storage[id];
                result[i].submenu = this.loadItemsCollapseState(item.submenu, id);
            }
        }
        return result;
    }
}

export default Menu;