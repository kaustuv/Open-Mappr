/*
   MooCirclePack
   version 0.1

   Based on http://www.cricketschirping.com/processing/CirclePacking1/CirclePacking1.pde
   and      http://en.wiki.mcneel.com/default.aspx/McNeel/2DCirclePacking

   Copyright (c) 2008 unwieldy studios
   
   Joshua Gross (unwieldy studios)
   twist at unwieldy dot net

   [MIT License]

   Permission is hereby granted, free of charge, to any person obtaining a copy
   of this software and associated documentation files (the "Software"), to deal
   in the Software without restriction, including without limitation the rights
   to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
   copies of the Software, and to permit persons to whom the Software is
   furnished to do so, subject to the following conditions:
   
   The above copyright notice and this permission notice shall be included in
   all copies or substantial portions of the Software.
   
   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
   IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
   FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
   AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
   LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
   OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
   THE SOFTWARE.
*/

// Class for each individual circle
var Circle = new Class({
   x: 0,
   y: 0,
   radius: 0,
   color: 'rgba(0, 0, 0, 1)',
   image: [],
   scale: 1,
   
   initialize: function(x, y, radius, color, image) {
      this.x = x;
      this.y = y;
      this.radius = radius;
      this.color = color;
      this.image = image;
      
      if($type(this.image) == 'object')
         this.image['object'] = new Asset.image(this.image.url); // preload the image
   },
   
   // scale is a multiplier that increases or decreases the size of the circle
   // it is mainly used for zooming.
   draw: function(canvas, scale) {
      this.scale = scale;
      
      var cx = canvas.getContext('2d');
      
      cx.beginPath();
      cx.arc(this.x, this.y, this.radius * scale, 0, Math.PI * 2, 0);
      cx.closePath();
      
      cx.fillStyle = this.color;
      cx.fill();
      
      // should probably have some way of making sure images are loaded or returned an error
      if(this.image && this.image['object'] != null) {
         var r = this.radius * scale;
         
         var imageCoords = {
            width: r * this.image['scale'],
            height: ((this.image['object'].height * r) / this.image['img'].width) * this.image['scale']
         };
         imageCoords['x'] = this.x - (imageCoords.width / 2);
         imageCoords['y'] = this.y - (imageCoords.height / 2);

         cx.drawImage(this.image['object'], imageCoords.x, imageCoords.y, imageCoords.width, imageCoords.height);
      }
   },
   
   distanceTo: function(x, y) {
      var dx = this.x - x;
      var dy = this.y - y;
      
      return Math.sqrt((dx*dx) + (dy*dy));
   },
   
   contains: function(x, y) {
      var dx = this.x - x;
      var dy = this.y - y;
      
      return Math.sqrt((dx*dx) + (dy*dy)) <= (this.radius * this.scale);
   },
   
   intersects: function(otherCircle) {
      var dx = otherCircle.x - this.x;
      var dy = otherCircle.y - this.y;
      
      var d  = Math.sqrt((dx*dx) + (dy*dy));
      
      return (d < (this.radius * this.scale)) || (d < (otherCircle.radius * this.scale));
   }
});

var Vector2D = new Class({
   x: 0,
   y: 0,
   
   initialize: function(x, y) {
      this.x = x;
      this.y = y;
   },
   
   normalize: function() {
      this.magnitude = Math.sqrt((this.x * this.x) + (this.y * this.y));
      
      this.x = this.x / this.magnitude;
      this.y = this.y / this.magnitude;
   },
   
   mult: function(m) {
      this.x *= m;
      this.y *= m;
   }
});

// Main circle packing class
var MooCirclePack = new Class({
   Implements: Events,
   
   canvas: null,
   circles: [],
   dragCircle: null,
   scale: 1,
   
   initialize: function(circles, canvas) {
      this.circles = circles;
      this.canvas = $(canvas);
   },
   
   compare: function(circle1, circle2) {
      var canvasPos = this.canvas.getCoordinates();
      
      var dtc1 = circle1.distanceTo(canvasPos.width / 2, canvasPos.height / 2);
      var dtc2 = circle2.distanceTo(canvasPos.width / 2, canvasPos.height / 2);
      
      if(dtc1 < dtc2)
         return 1;
      else if(dtc1 > dtc2)
         return -1;
      else
         return 0;
   },
   
   iterator: function(iteration) {
      this.circles.sort(this.compare.bind(this));
      
      var ci, cj;
      var v = new Vector2D();
      
      for(var i = 0; i < this.circles.length; i++) {
         ci = this.circles[i];
         
         for(var j = i + 1; j < this.circles.length; j++) {
            if(i == j)
               continue;
               
            cj = this.circles[j];
            
            var dx = cj.x - ci.x;
            var dy = cj.y - ci.y;
            var d  = (dx * dx) + (dy * dy);
            var r  = (cj.radius * this.scale) + (ci.radius * this.scale);
            
            if(d < ((r * r) - 0.01)) {
               v.x = dx;
               v.y = dy;
               v.normalize();
               v.mult((r - Math.sqrt(d)) * 0.5);
               
               if(cj != this.dragCircle) {
                  cj.x += v.x;
                  cj.y += v.y;
               }

               if(ci != this.dragCircle) {
                  ci.x -= v.x;
                  ci.y -= v.y;
               }
            }
         }
      }

      // decrease the amount that the circles are contracted by 2% each time,
      // providing the "settling" effect/objective.
      this.damping *= 0.98;
      this.contract(this.damping);
   },
   
   contract: function(damping) {
      if(damping < 0.01)
         return;
      
      var x, y;   
      
      var canvasPos = this.canvas.getCoordinates();
      
      for(var i = 0; i < this.circles.length; i++) {
         if(this.circles[i] == this.dragCircle)
            continue;
            
         x = (this.circles[i].x - (canvasPos.width / 2)) * damping;
         y = (this.circles[i].y - (canvasPos.height / 2)) * damping;

         this.circles[i].x -= x;
         this.circles[i].y -= y;
      }
   },
   
   draw: function() {
      var canvasPos = this.canvas.getCoordinates();

      var cx = this.canvas.getContext('2d');
      cx.clearRect(0, 0, canvasPos.width, canvasPos.height);
      
      for(var i = 0; i < this.circles.length; i++)
         this.circles[i].draw(this.canvas, this.scale);
   },
   
   run: function(it) {
      this.iteration = it ? it : 1;
      this.damping = it ? Math.pow(0.98, it) * 0.1 : 0.1;
      
      if(this.runner)
         $clear(this.runner);
      
      // Unfortunately, in browsers, it is currently impractical to run a for-loop
      // on the circle packing algorithm. Instead, we do it with a timer which,
      // while more sloppy, allows the processor to breathe and prevents the browser
      // from locking up.
      this.runner = this._run.periodical(30, this);
      
      this.fireEvent('settleStart');
   },
   
   _run: function() {
      this.draw();
      
      this.iterator(this.iteration++);
      
      // stop the iterator at a reasonable point, to free up the processor
      if(this.damping < 0.007) {
         $clear(this.runner);
         
         this.fireEvent('settleEnd');
      }
   },

   mouseDown: function(x, y) {
      this.dragCircle = null;
      for(var i = 0; i < this.circles.length; i++) {
         if (this.circles[i].contains(x, y)) {
            this.dragCircle = this.circles[i];
            
            // 35 seems to be the optimal number to "restart" the iteration at
            // when an object is moved.
            this.run(35);
            
            this.fireEvent('dragStart', this.dragCircle);
            break;
         }
      }
   },
   
   mouseMove: function(x, y) {
      if(this.dragCircle != null) {
         this.dragCircle.x = x;
         this.dragCircle.y = y;
         this.iteration = 35;
         
         this.fireEvent('onDrag', [this.dragCircle, x, y]);
      }

      if(!this.dragCircle) {
         for(var i = 0; i < this.circles.length; i++) {
            if (this.circles[i].contains(x, y)) {
               if(this.mouseOverCircle == this.circles[i])
                  break;
               else if(this.mouseOverCircle)
                  this.fireEvent('mouseOut', [this.mouseOverCircle, x, y]);
               
               this.mouseOverCircle = this.circles[i];
               this.fireEvent('mouseOver', [this.mouseOverCircle, x, y]);
               break;
            }
         }
         
         if(i == this.circles.length) {
            if(this.mouseOverCircle)
               this.fireEvent('mouseOut', [this.mouseOverCircle, x, y]);
               
            this.mouseOverCircle = null;
         }
      }
   },
   
   mouseUp: function() {
      if(this.dragCircle) this.fireEvent('dragEnd', this.dragCircle);
      
      this.dragCircle = null;
   },
   
   hook: function() {
      window.addEvent('unload', this.unhook.bind(this));
      
      this.mouseDownEvent = function(e) {
         var canvasPos = this.canvas.getCoordinates();            
   
         e = new Event(e);
         
         var x = e.page.x - canvasPos.left;
         var y = e.page.y - canvasPos.top;
         
         this.mouseDown(x, y);
      }.bind(this);
      this.canvas.addEvent('mousedown', this.mouseDownEvent);
      
      this.mouseMoveEvent = function(e) {
         var canvasPos = this.canvas.getCoordinates();            
   
         e = new Event(e);
         
         var x = e.page.x - canvasPos.left;
         var y = e.page.y - canvasPos.top;
         
         this.mouseMove(x, y);
      }.bind(this);
      this.canvas.addEvent('mousemove', this.mouseMoveEvent);
      
      this.mouseUpEvent = this.mouseUp.bind(this);
      this.canvas.addEvent('mouseup', this.mouseUpEvent);
      
      this.canvas.addEvent('mousewheel', function(e) {
         e = new Event(e);
         e.stop();
         
         if((this.scale > 14 && e.wheel > 0) || (this.scale <= 0.5 && e.wheel < 0))
            return;
         
         this.scale += e.wheel > 0 ? 0.5 : -0.5;
         
         this.run();
      }.bind(this));
   },
   
   unhook: function() {
      this.canvas.removeEvent('mousedown', this.mouseDownEvent);
      this.canvas.removeEvent('mousemove', this.mouseMoveEvent);
      this.canvas.removeEvent('mouseup', this.mouseUpEvent);
      
      delete this.mouseDownEvent;
      delete this.mouseMoveEvent;
      delete this.mouseUpEvent;
   }
});

// Remote retrieval class
MooCirclePack.Remote = new Class({
   Extends: MooCirclePack,
   
   initialize: function(url, method, query, canvas) {
      this.canvas = canvas;
      
      var req = new Request.JSON({'url': url, 'method': method, 'onComplete': function(circles) {
         var c = $A([]);

         $A(circles).each(function(circle) {
            c.push(new Circle(circle.x, circle.y, circle.radius, circle.color, circle.image ? circle.image : null));
         });
         
         this.circles = c;
      }.bind(this)}).send(query);
   }
});