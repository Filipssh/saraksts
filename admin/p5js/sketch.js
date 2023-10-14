let balls = [];
let c;

class Ball{
  constructor(){
    this.x = random(0,windowWidth);
    this.y = random(0,windowHeight);
    this.d = random(50,200);
    this.vX = random(-0.5,0.5);
    this.vY = random(-0.5,0.5);
  }
  show(){
    fill(c);
    noStroke();
    circle(this.x,this.y,this.d);
  }
  update(){
    this.x += this.vX;
    this.y += this.vY;
    if(this.x < 0){
      this.x = 0;
      this.vX = -this.vX;
    }
    if(this.x > windowWidth){
      this.x = windowWidth;
      this.vX = -this.vX;
    }
    if(this.y < 0){
      this.y = 0;
      this.vY = -this.vY;
    }
    if(this.y > windowHeight){
      this.y = windowHeight;
      this.vY = -this.vY;
    }
  }
}

function setup() {
  createCanvas(windowWidth,windowHeight);
  const amount = sqrt(windowWidth * windowHeight) / 20; // mazāk bumbas uz mazākiem ekrāniem
  for(let i=0; i<amount;i++){
    balls.push(new Ball());
  }
  c = color(197, 252, 247);
}

function draw() {
  clear();
  
  for(let ball of balls){
    ball.show();
    ball.update();
  }
}

function windowResized() {
  resizeCanvas(windowWidth,windowHeight);
}
