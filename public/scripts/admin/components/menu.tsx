import React from 'react';
import Item, { ItemProps } from './menu/Item';

interface Props {
    items: ItemProps[]
}

class Menu extends React.Component<Props> {
    public render(): React.ReactNode {
        return <ul className="menu">
            {this.props.items.map((item, index) => <Item
                key={index}
                icon={item.icon}
                name={item.name}
                link={item.link}
                submenu={item.submenu}
            ></Item>)}
        </ul> 
    }
}

export default Menu;