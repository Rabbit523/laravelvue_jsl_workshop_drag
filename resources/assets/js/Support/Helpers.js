export default {
    install: function (Vue, options) {
        Vue.directive('drag-and-drop', {
            bind: (el, binding, vnode) => {

                this.handleDragStart = function (e) {
                    e.target.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                    // Need to set to something or else drag doesn't start
                    e.dataTransfer.setData('text', '*');
                    vnode.context.$emit('drag-start');
                };

                this.handleDragOver = function (e) {
                    if (e.preventDefault) {
                        // allows dropping
                        e.preventDefault();
                    }

                    e.dataTransfer.dropEffect = 'move';
                    e.target.classList.add('drag-over', 'slot-dropzone');
                    vnode.context.$emit('drag-over');

                    return false;
                };

                this.handleDragEnter = function (e) {
                    vnode.context.$emit('drag-enter');
                    if (e.target.classList != undefined) {
                        e.target.classList.add('drag-enter');
                    }
                };

                this.handleDrag = function () {
                    vnode.context.$emit('drag');
                };

                this.handleDragLeave = function (e) {
                    vnode.context.$emit('drag-leave');
                    if (e.target.classList != undefined) {
                        e.target.classList.remove('drag-enter', 'drag-over', 'slot-dropzone');
                    }
                };

                this.handleDragEnd = function (e) {
                    e.target.classList.remove('dragging', 'drag-over', 'drag-enter', 'slot-dropzone');
                    vnode.context.$emit('drag-end');
                };

                this.handleDrop = function (e) {
                    e.preventDefault();
                    if (e.stopPropagation) {
                        // stops the browser from redirecting.
                        e.stopPropagation();
                    }
                    e.target.classList.remove('dragging', 'drag-over', 'drag-enter', 'slot-dropzone');
                    // Don't do anything if dropping the same column we're dragging.
                    vnode.context.$emit('drop');

                    return false;
                };



                // setup the listeners
                el.setAttribute('draggable', 'true');
                el.addEventListener('dragstart', this.handleDragStart, false);
                el.addEventListener('dragenter', this.handleDragEnter, false);
                el.addEventListener('dragover', this.handleDragOver, false);
                el.addEventListener('drag', this.handleDrag, false);
                el.addEventListener('dragleave', this.handleDragLeave, false);
                el.addEventListener('dragend', this.handleDragEnd, false);
                el.addEventListener('drop', this.handleDrop, false);
            },
            unbind: (el) => {
                // shut er' down
                // console.log(el);
                el.classList.remove('dragging', 'drag-over', 'drag-enter', 'slot-dropzone');
                el.removeAttribute('draggable');
                el.removeEventListener('dragstart', this.handleDragStart);
                el.removeEventListener('dragenter', this.handleDragEnter);
                el.removeEventListener('dragover', this.handleDragOver);
                el.removeEventListener('dragleave', this.handleDragLeave);
                el.removeEventListener('drag', this.handleDrag);
                el.removeEventListener('drop', this.handleDrop);
            }
        });

    }
};
