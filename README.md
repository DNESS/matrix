# Matrix

The matrix for your terminal.

![Matrix](https://github.com/prggmr/matrix/raw/master/image.png)

## Requirements

* [XPSPL](http://xpspl.prggmr.org/ "XPSPL")

## Installation

```sh

git clone git://github.com/prggmr/matrix.git ~/.matrix
```

Install to bin.

```sh

sudo ln -s $HOME/.matrix/matrix /usr/bin
```

Then run

```sh
matrix
```

## Performance

The matrix currently can run within microsecond cycles.

By default the interval is set to 85.

Turn the ```-f``` option on to see the performance stats.

To achieve microsecond cycles simply set the interval ```-i``` to one number +/- the ```AVG Process Time``` and adjust 
so it stays above negative.

## Memory

There currently exists a memory leak ... somewhere ... maybe you can find it ... 

The leak is rather small and adds rather slowely ... but it exists!!
