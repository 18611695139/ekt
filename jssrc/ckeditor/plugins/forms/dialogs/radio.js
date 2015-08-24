/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.dialog.add( 'radio', function( editor ) {
	return {
		title: editor.lang.forms.checkboxAndRadio.radioTitle,
		minWidth: 350,
		minHeight: 140,
		onShow: function() {
			delete this.radioButton;

			var element = this.getParentEditor().getSelection().getSelectedElement();
			if ( element && element.getName() == 'input' && element.getAttribute( 'type' ) == 'radio' ) {
				this.radioButton = element;
				this.setupContent( element );
			}
		},
		onOk: function() {
			var editor,
				element = this.radioButton,
				isInsertMode = !element,
                radios = this.getContentElement('info', 'txtValue').getValue() || '';
                radios = radios.split("\n");

			if ( isInsertMode ) {
				editor = this.getParentEditor();
                var elements = [];
                //循环插入多个元素
                for(var i=0;i<radios.length;i++){
                    if(radios[i]){
                        //处理值和标签
                        if(radios[i].indexOf('#')){
                            var str = radios[i].split('#');
                            var value = str[0];
                            var label = str[1] || str[0];
                        }
                        else{
                            var value = radios[i];
                            var label = radios[i];
                        }
                        
                        elements[i] = editor.document.createElement( 'label' );
                        var child = editor.document.createElement( 'input' );
        				child.setAttribute( 'type', 'radio' );
                        child.setAttribute( 'value', value );
                        child.appendTo(elements[i]);
                        elements[i].appendHtml(label);
        				editor.insertElement( elements[i] );
                    }
                }
			}

			this.commitContent({ element: elements } );
		},
		contents: [
			{
			id: 'info',
			label: editor.lang.forms.checkboxAndRadio.radioTitle,
			title: editor.lang.forms.checkboxAndRadio.radioTitle,
			elements: [
				{
				id: 'name',
				type: 'text',
				label: editor.lang.common.name,
				'default': '',
				accessKey: 'N',
				setup: function( element ) {
					this.setValue( element.data( 'cke-saved-name' ) || element.getAttribute( 'name' ) || '' );
				},
				commit: function( data ) {
					var elements = data.element;
                    
                    for(var i=0;i<elements.length;i++){
                        var element = elements[i].getChild(0);
                        if ( this.getValue() )
    						element.data( 'cke-saved-name', this.getValue() );
    					else {
    						element.data( 'cke-saved-name', false );
    						element.removeAttribute( 'name' );
    					}
                    }
				}
			},
				{
				id: '_cke_saved_label',
				type: 'text',
				label: '标签',
				'default': '',
				accessKey: 'N',
                validate: function(){
                    if(!this.getValue()){
                        alert('标签不能为空');
                        return false;
                    }
                },
				setup: function( element ) {
					this.setValue( element.data( 'cke-saved-label' ) || element.getAttribute( 'label' ) || '' );
				},
				commit: function( data ) {
				    var elements = data.element;
                    
                    for(var i=0;i<elements.length;i++){
                        var element = elements[i].getChild(0);
    					if ( this.getValue() )
    						element.data( 'cke-saved-label', this.getValue() );
    					else {
    						element.data( 'cke-saved-label', false );
    						element.removeAttribute( 'label' );
    					}
                    }
				}
			},
				{
				id: 'txtValue',
				type: 'textarea',
				label: '选项值(每行一个选项，格式：值#标签)',
				'default': '',
				accessKey: 'V',
				setup: function( element ) {
					
				},
				commit: function( data ) {
					
				}
			}
			]
		}
		]
	};
});
