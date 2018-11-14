final int SIZE = 50;
final int STONE_SIZE = (int)(SIZE*0.7);
final int NONE = 0;
final int BLACK = 1;
final int WHITE = 2;

int[][] field;
boolean black_turn = true;
int reach=0;
boolean left=false;
boolean right=false;
boolean up=false;
boolean down=false;
boolean upLeft=false;
boolean upRight=false;
boolean downLeft=false;
boolean downRight=false;
int num=0;
int wait=0;
boolean[][] visited=new boolean[8][8];
int snum=0;

int [][]AI_field = {
  {120, -20, 20, 5, 5, 20, -20, 120}, 
  {-20, -40, -5, -5, -5, -5, -40, -20}, 
  { 20, -5, 15, 3, 3, 15, -5, 20}, 
  {  5, -5, 3, 3, 3, 3, -5, 5}, 
  {  5, -5, 3, 3, 3, 3, -5, 5}, 
  { 20, -5, 15, 3, 3, 15, -5, 20}, 
  {-20, -40, -5, -5, -5, -5, -40, -20}, 
  {120, -20, 20, 5, 5, 20, -20, 120}
};

void setup() {
  size(400, 400);//8*SIZE,8*SIZE);
  field = new int[8][8];
  for (int i=0; i<8; ++i) {
    for (int j=0; j<8; ++j) {
      field[i][j] = NONE;
    }
  }
  initialization();
  save("othello"+snum+".png");
  snum++;
}

void initialization() {
  field[4][3] = BLACK;
  field[3][3] = WHITE;
  field[4][4] = WHITE;
  field[3][4] = BLACK;

  //  for (int i=0; i<8; i++) {
  //    field[i][0] = BLACK;
  //    field[7][i] = BLACK;
  //  }
  //  field[6][1] = WHITE;
  //  field[6][4] = WHITE;
}


void draw() {

  background(0, 128, 0);

  // lines
  stroke(0);
  for (int i=1; i<8; ++i) {
    line(i*SIZE, 0, i*SIZE, height);
    line(0, i*SIZE, width, i*SIZE);
  }

  // draw stones
  noStroke();
  for (int i=0; i<8; i++) {
    for (int j=0; j<8; j++) {

      if (field[i][j]==BLACK) {
        fill(0);  //color black
        ellipse((i*2+1)*SIZE/2, (j*2+1)*SIZE/2, STONE_SIZE, STONE_SIZE);
      } else if (field[i][j]==WHITE) {
        fill(255); // color white
        ellipse((i*2+1)*SIZE/2, (j*2+1)*SIZE/2, STONE_SIZE, STONE_SIZE);
      }
    }
  }
}

void mousePressed() {
  int x = mouseX/SIZE;
  int y = mouseY/SIZE;

  if (field[x][y]==NONE) {
    if (can_put_here(x, y)==true) {

      if (black_turn) {
        field[x][y]=BLACK;
      } else {
        field[x][y]=WHITE;
      }
      if (left)reverseField(x, y, -1, 0);
      if (right)reverseField(x, y, 1, 0);  
      if (up)reverseField(x, y, 0, -1);
      if (down)reverseField(x, y, 0, 1);
      if (upLeft)reverseField(x, y, -1, -1);
      if (upRight)reverseField(x, y, 1, -1);
      if (downLeft)reverseField(x, y, -1, 1);
      if (downRight)reverseField(x, y, 1, 1);
      black_turn = !black_turn;

      auto_play();
    }
  }
    save("othello"+snum+".png");
  snum++;
}

int get_current_stone() {
  if (black_turn) return BLACK;
  else return WHITE;
}


void auto_play() {
  int maxi=0;
  int maxj=0;  //ここに一番評価値の高い座標i,jが代入される
  //石を置く前に、石をおける場所をfor文i,jで計算して、一番スコアが大きいものの座標に石を置く
  int score=-40;

  //if(wait==0){
  //  wait++;
  //  return;
  //}else{
  //delay(1000);
  //wait=0;
  //}


  for (int i=0; i<8; i++) {
    for (int j=0; j<8; j++) {
      boolean put = can_put_here(i, j);
      if (put) {//置けるところが見つかったら
        //println("開放度　=", check_open(i, j));
        if (score<=(AI_field[i][j]-check_open(i, j)-1)) {//前の盤面評価値より大きい場合、
          maxi=i;
          maxj=j;
          score=AI_field[i][j]-check_open(i, j);
        }
      }
    }
  }
  println("i = "+maxi+"J = "+maxj);
  println(score);
  println("");
  boolean put = can_put_here(maxi, maxj);
  if (put) {
    field[maxi][maxj]=get_current_stone(); 
    if (left)reverseField(maxi, maxj, -1, 0);
    if (right)reverseField(maxi, maxj, 1, 0);
    if (up)reverseField(maxi, maxj, 0, -1);
    if (down)reverseField(maxi, maxj, 0, 1);
    if (upLeft)reverseField(maxi, maxj, -1, -1);
    if (upRight)reverseField(maxi, maxj, 1, -1);
    if (downLeft)reverseField(maxi, maxj, -1, 1);
    if (downRight)reverseField(maxi, maxj, 1, 1);
    black_turn=!black_turn;
    return;
  } else {//白がおけない時、パスする
    black_turn=!black_turn;
    return;
  }



}




void reverseField(int intx, int inty, int vecx, int vecy) {

  if (field[intx+vecx][inty+vecy]!=get_current_stone()) {
    field[intx+vecx][inty+vecy]=get_current_stone();
    reverseField(intx+vecx, inty+vecy, vecx, vecy);
  }
}





boolean can_put_here(int intx, int inty) {
  boolean puttable=false;

  if (field[intx][inty]!=NONE) {
    return false;
  } else {

    left=check_direction(intx, inty, -1, 0);
    right=check_direction(intx, inty, 1, 0);
    up=check_direction(intx, inty, 0, -1);
    down=check_direction(intx, inty, 0, 1);

    upLeft=check_direction(intx, inty, -1, -1);
    upRight=check_direction(intx, inty, 1, -1);
    downRight=check_direction(intx, inty, 1, 1);
    downLeft=check_direction(intx, inty, -1, 1);
  }
  if (left) {
    println("left = "+left);
  }
  if (right) {
    println("right = "+right);
  }
  if (up) {
    println("up = "+up);
  }
  if (down) {
    println("down = "+down);
  }

  if (upLeft) {
    println("upLeft = "+upLeft);
  }
  if (upRight) {
    println("upRight = "+upRight);
  }
  if (downLeft) {
    println("downLeft = "+downRight);
  }
  if (downRight) {
    println("downRight = "+downRight);
  }

  if (left || right || up || down || upLeft || upRight || downRight || downLeft) {
    puttable =true;
  }


  return puttable;
}


void reverseField_pure(int intx, int inty, int vecx, int vecy) {

  if (field[intx+vecx][inty+vecy]!=get_current_stone()) {
    println("ひっくり返される玉 =",intx+vecx, inty+vecy);
    reverseField_pure(intx+vecx, inty+vecy, vecx, vecy);
  }
}

int check_open(int x, int y) {//ひっくり返すために置くコマの座標
  int num=0;
  //boolean[][] visited=new boolean[8][8];
  for (int i=0; i<8; i++) {
    for (int j=0; j<8; j++) {
      visited[i][j]=false;
    }
  }
  //ひっくり返す前にひっくり返る石の周りの開放度を求める
  //ひっくり返る石を調べる

      for (int i=-1; i<=1; i++) {
    for (int j=-1; j<=1; j++) {
      if (0<=(x+i)&&(x+i)<8&&0<=(y+j)&&(y+j)<8) {
        if (field[x+i][y+j]==NONE && !visited[x+i][y+j]) {
          num++;
          visited[x+i][y+j]=true;
        }
      }
    }
  }
  
  return num;
}




boolean check_direction(int intx, int inty, int vecx, int vecy) {
  if (0<=intx+vecx&&intx+vecx<8 && 0<=inty+vecy&&inty+vecy<8) {
    if (field[intx+vecx][inty+vecy]==get_current_stone() || field[intx+vecx][inty+vecy]==NONE ) {
      return false;
    } else {

      return check_direction_sub(intx+vecx, inty+vecy, vecx, vecy);
    }
  } else {

    return false;
  }
}

boolean check_direction_sub(int intx, int inty, int vecx, int vecy) {
  if (0<=intx+vecx&&intx+vecx<8 && 0<=inty+vecy&&inty+vecy<8) {
    if (field[intx+vecx][inty+vecy]==get_current_stone()) {

      return true;
    } else if (field[intx+vecx][inty+vecy]==NONE) {
      return false;
    } else {

      return check_direction_sub(intx+vecx, inty+vecy, vecx, vecy);
    }
  } else {
    return false;
  }
}
