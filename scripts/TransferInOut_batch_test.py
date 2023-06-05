#!/usr/bin/python
# -*- coding: UTF-8 -*-

# from concurrent.futures import ThreadPoolExecutor
# import threading
import time
import requests
from multiprocessing import Pool

def f(row):
    r = requests.post('http://server.tongits-yfb.com/api/cash/transferInOut?_t=1655120877&_s=52Q6rvZXeJqMbAJPwVkwBg0l1oNpw3YO', json=row)
    # print(r.status_code)
    # print(r.json())
    return r.json()


if __name__ == '__main__':
    max_num = 1000
    num = 0
    while True:
        num = num + 1
        try:
            r = requests.get('http://h5.tongitsq-yfb01.com/api/test/batchInsertTransferInout?_t=1655120877&_s=52Q6rvZXeJqMbAJPwVkwBg0l1oNpw3YO')
            # print(r.status_code)
            res = r.json()
            # print(res)
            with Pool(8) as p:
                p.map(f, res)
        except e:
            print(e)
        finally:
            break
            time.sleep(10)
        
        if num >= max_num:
            print("auto break")
            break
        


