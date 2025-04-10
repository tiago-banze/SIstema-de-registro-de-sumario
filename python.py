lista=[]
# for i in range(201):
#     if i%3==0:
#         if i!=0:
#             lista.append(i)
# print(lista)
# print(f'A lista tem {len(lista)} elementos!')
i=int(input('quantos primeiros multiplos de 2 gostarias de ver? : '))
cont = 0
n= int(input('Começando de? : '))
while True:
    n=1+n
    if n%2==0:
        if n!=0:
            lista.append(n)
            cont=cont+1
    if cont == i:
        break
print(lista)
print(f'A lista tem {len(lista)} elementos!')
        