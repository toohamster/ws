< ? p h p   n a m e s p a c e   W s \ D b ;  
  
 / * *  
   *   �{f�v  ;N�N  �[�s 
   * /  
 c l a s s   S q l M S  
 {  
  
 	 / * *  
 	   *   ;N�^penc�n 
 	   *   @ v a r   S q l D a t a S o u r c e  
 	   * /  
 	 p r i v a t e   s t a t i c   $ d s _ m a s t e r ;  
  
 	 / * *  
 	   *   �N�^penc�n 
 	   *   @ v a r   S q l D a t a S o u r c e  
 	   * /  
 	 p r i v a t e   s t a t i c   $ d s _ s l a v e r ;  
  
 	 p u b l i c   s t a t i c   f u n c t i o n   i n i t ( a r r a y   $ c o n f i g )  
 	 {  
 	 	 s e l f : : $ d s _ m a s t e r   =   S q l : : d s ( $ c o n f i g [ ' m a s t e r ' ] ) ;  
 	 	 s e l f : : $ d s _ s l a v e r   =   S q l : : d s ( $ c o n f i g [ ' s l a v e r ' ] ) ;  
 	 }  
  
 	 / * *  
 	   *   ԏ�V;N�^�[a� 
 	   *    
 	   *   @ r e t u r n   \ W s \ D b \ S q l M a s t e r  
 	   * /  
 	 p u b l i c   s t a t i c   f u n c t i o n   m a s t e r ( )  
 	 {  
 	 	 s t a t i c   $ m a s t e r   =   n u l l ;  
 	 	 i f   ( i s _ n u l l ( $ m a s t e r ) )   $ m a s t e r   =   n e w   S q l M a s t e r ( s e l f : : $ d s _ m a s t e r ) ;  
 	 	 r e t u r n   $ m a s t e r ;  
 	 }  
  
 	 / * *  
 	   *   ԏ�V;N�^�[a� 
 	   *    
 	   *   @ r e t u r n   \ W s \ D b \ S q l S l a v e r  
 	   * /  
 	 p u b l i c   s t a t i c   f u n c t i o n   s l a v e r ( )  
 	 {  
 	 	 s t a t i c   $ s l a v e r   =   n u l l ;  
 	 	 i f   ( i s _ n u l l ( $ s l a v e r ) )   $ s l a v e r   =   n e w   S q l S l a v e r ( s e l f : : $ d s _ s l a v e r ) ;  
 	 	 r e t u r n   $ s l a v e r ;  
 	 }  
  
 }  
  
 / * *  
   *   �N�^  �N�Nb}�@w  " ��"   penc�v�R�� 
   * /  
 c l a s s   S q l S l a v e r  
 {  
  
 	 / * *  
 	   *   �g ��Qpe 
 	   *    
 	   *   @ p a r a m   $ d s  
 	   * /  
 	 p u b l i c   f u n c t i o n   _ _ c o n s t r u c t ( $ d s )  
 	 {  
 	 	 $ t h i s - > d s   =   $ d s ;  
 	 }  
  
 	 # #   ��]\ň N�N  ���d\O 
 	  
         / * *  
           *   �Nh�-N�h"}&{Tag�N�v Nag��U_ 
           *  
           *   @ p a r a m   s t r i n g   $ t a b l e  
           *   @ p a r a m   m i x e d   $ c o n d  
           *   @ p a r a m   s t r i n g   $ f i e l d s  
           *   @ p a r a m   s t r i n g   $ s o r t  
           *    
           *   @ r e t u r n   a r r a y  
           * /  
         p u b l i c   f u n c t i o n   s e l e c t _ r o w ( $ t a b l e ,   $ c o n d = n u l l ,   $ f i e l d s = ' * ' ,   $ s o r t = n u l l )  
         {  
         	 r e t u r n   S q l : : a s s i s t a n t (   $ t h i s - > d s   ) - > s e l e c t _ r o w ( $ t a b l e ,   $ c o n d ,   $ f i e l d s ,     $ s o r t ) ;  
         }  
  
         / * *  
 	   *   �Nh�-N�h"}&{Tag�N�vYag��U_ 
 	   *  
 	   *   @ p a r a m   s t r i n g   $ t a b l e  
 	   *   @ p a r a m   m i x e d   $ c o n d  
 	   *   @ p a r a m   s t r i n g   $ f i e l d s  
 	   *   @ p a r a m   s t r i n g   $ s o r t  
 	   *   @ p a r a m   i n t | a r r a y   $ l i m i t   pe�~�v݋u��_<h_  (   o f f s e t , l e n g t h   )    
 	   *   @ p a r a m   b o o l   $ c a l c   ���{;`*Npe   
 	   *    
 	   *   @ r e t u r n   a r r a y  
 	   * /  
 	 p u b l i c   f u n c t i o n   s e l e c t ( $ t a b l e ,   $ c o n d = n u l l ,   $ f i e l d s = ' * ' ,   $ s o r t = n u l l ,   $ l i m i t = n u l l ,   $ c a l c = f a l s e )  
 	 {  
 	 	 r e t u r n   S q l : : a s s i s t a n t (   $ t h i s - > d s   ) - > s e l e c t ( $ t a b l e ,   $ c o n d ,   $ f i e l d s ,   $ s o r t ,   $ l i m i t ,   $ c a l c ) ;  
 	 }  
  
         / * *  
           *   �~��&{Tag�N�v��U_�v;`pe 
           *  
           *   @ p a r a m   s t r i n g   $ t a b l e  
           *   @ p a r a m   m i x e d   $ c o n d  
           *   @ p a r a m   s t r i n g | a r r a y   $ f i e l d s  
           *   @ p a r a m   b o o l e a n   $ d i s t i n c t  
           *  
           *   @ r e t u r n   i n t  
           * /  
         p u b l i c   f u n c t i o n   c o u n t ( $ t a b l e ,   $ c o n d = n u l l ,   $ f i e l d s = ' * ' ,   $ d i s t i n c t = f a l s e )  
         {  
         	 r e t u r n   S q l : : a s s i s t a n t (   $ t h i s - > d s   ) - > c o u n t ( $ t a b l e ,   $ c o n d ,   $ f i e l d s ,   $ d i s t i n c t ) ;  
         }  
  
         / * *  
 	   *   gbL�  ��  �d\O 
 	   *    
 	   *   @ p a r a m   s t r i n g   $ m o d e   !j_  [ M O D E _ R E A D _ G E T A L L , M O D E _ R E A D _ G E T R O W , M O D E _ R E A D _ G E T O N E , M O D E _ R E A D _ G E T C O L ]  
 	   *   @ p a r a m   m i x e d   $ a r g s   �Spe[ NT!j_�SpeNT, :w:Ns q l W[&{2N]  
 	   *   @ p a r a m   c a l l b a c k   $ c b   �g⋰�U_Ɩ�v�V�Yt�Qpe 
 	   *    
 	   *   @ r e t u r n   m i x e d  
 	   * /  
 	 p u b l i c   f u n c t i o n   r e a d ( $ m o d e ,   $ a r g s ,   $ c b = N U L L )  
 	 {  
 	 	 r e t u r n   S q l : : r e a d (   $ t h i s - > d s ,   $ m o d e ,   $ a r g s ,   $ c b ) ;  
 	 }  
  
 }  
  
  
 / * *  
   *   ;N�^  �SR
N�N�Nb}�@w  " �Q"   penc�v�R�� 
   *   *   FO/f(WTek�vǏz-N1u�NQ�~�^ߏ �bpencNTek, �MQ�penc�v�Nu 
   *   *   b�  �N�RYtǏz-N_N ���  " ��"    
   * /  
 c l a s s   S q l M a s t e r   e x t e n d s   S q l S l a v e r  
 {  
  
 	 / * *  
 	   *   �g ��Qpe 
 	   *    
 	   *   @ p a r a m   $ d s  
 	   * /  
 	 p u b l i c   f u n c t i o n   _ _ c o n s t r u c t ( $ d s )  
 	 {  
 	 	 $ t h i s - > d s   =   $ d s ;  
 	 }  
  
 	 # #   ��]\ň N�N  �Q�d\O 
 	          
         / * *  
           *   �ceQ Nag��U_ 
           *  
           *   @ p a r a m   s t r i n g   $ t a b l e  
           *   @ p a r a m   a r r a y   $ r o w  
           *   @ p a r a m   b o o l   $ p k v a l   /f&T���S�ceQ�v;N.�<P 
           *  
           *   @ r e t u r n   m i x e d  
           * /  
         p u b l i c   f u n c t i o n   i n s e r t ( $ t a b l e ,   a r r a y   $ r o w ,   $ p k v a l = f a l s e )  
         {  
         	 r e t u r n   S q l : : a s s i s t a n t (   $ t h i s - > d s   ) - > i n s e r t ( $ t a b l e ,   $ r o w ,   $ p k v a l ) ;  
 	 }  
  
         / * *  
 	   *   �f�eh�-N��U_ 
 	   *  
 	   *   @ p a r a m   s t r i n g   $ t a b l e  
 	   *   @ p a r a m   a r r a y   $ r o w  
 	   *   @ p a r a m   m i x e d   $ c o n d   ag�N 
 	   *    
 	   *   @ r e t u r n   i n t  
 	   * /  
 	 p u b l i c   f u n c t i o n   u p d a t e ( $ t a b l e ,   a r r a y   $ r o w ,   $ c o n d = n u l l )  
 	 {  
 	 	 r e t u r n   S q l : : a s s i s t a n t (   $ t h i s - > d s   ) - > u p d a t e ( $ t a b l e ,   $ r o w ,   $ c o n d ) ;  
 	 }  
  
         / * *  
 	   *    Rd�  h�-N��U_ 
 	   *    
 	   *   @ p a r a m   s t r i n g   $ t a b l e  
 	   *   @ p a r a m   m i x e d   $ c o n d  
 	   *    
 	   *   @ r e t u r n   i n t  
 	   * /  
 	 p u b l i c   f u n c t i o n   d e l ( $ t a b l e ,   $ c o n d = n u l l )  
 	 {  
 	 	 r e t u r n   S q l : : a s s i s t a n t (   $ t h i s - > d s   ) - > d e l ( $ t a b l e ,   $ c o n d ) ;  
 	 }  
  
 	 / * *  
 	   *   Th�-N  �gW[�k�v<PZP  " �R" Џ�{ 
 	   *  
 	   *   @ p a r a m   s t r i n g   $ t a b l e  
 	   *   @ p a r a m   s t r i n g   $ f i e l d  
 	   *   @ p a r a m   i n t   $ i n c r  
 	   *   @ p a r a m   m i x e d   $ c o n d  
 	   *    
 	   *   @ r e t u r n   i n t  
 	   * /  
         p u b l i c   f u n c t i o n   i n c r _ f i e l d ( $ t a b l e ,   $ f i e l d ,   $ i n c r   =   1 ,   $ c o n d = n u l l )  
         {  
         	 r e t u r n   S q l : : a s s i s t a n t (   $ t h i s - > d s   ) - > i n c r _ f i e l d ( $ t a b l e ,   $ f i e l d ,   $ i n c r ,   $ c o n d ) ;  
         }  
  
         / * *  
 	   *   gbL�  �f�e/  Rd�  �d\O 
 	   *  
 	   *   @ p a r a m   s t r i n g   $ m o d e   !j_  [ M O D E _ W R I T E _ I N S E R T , M O D E _ W R I T E _ U P D A T E , M O D E _ W R I T E _ D E L E T E ]  
 	   *   @ p a r a m   m i x e d   $ a r g s   �Spe[ NT!j_�SpeNT, :w:Ns q l W[&{2N]  
 	   *   @ p a r a m   c a l l b a c k   $ c b   �g��~�gƖ�v�V�Yt�Qpe 
 	   *    
 	   *   @ r e t u r n   m i x e d  
 	   * /  
 	 p u b l i c   f u n c t i o n   w r i t e ( $ m o d e ,   $ a r g s ,   $ c b = N U L L )  
 	 {  
 	 	 r e t u r n   S q l : : w r i t e (   $ t h i s - > d s ,   $ m o d e ,   $ a r g s ,   $ c b ) ;  
 	 }  
  
 }  
 